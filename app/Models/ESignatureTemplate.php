<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ESignatureTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name', 'template_code', 'description', 'document_type', 'signature_fields',
        'approval_workflow', 'signature_requirements', 'expiry_days', 'requires_witness',
        'requires_notarization', 'notification_settings', 'security_settings', 'is_active',
        'sort_order', 'metadata'
    ];

    protected $casts = [
        'signature_fields' => 'array',
        'approval_workflow' => 'array',
        'signature_requirements' => 'array',
        'requires_witness' => 'boolean',
        'requires_notarization' => 'boolean',
        'notification_settings' => 'array',
        'security_settings' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array'
    ];

    // Relationships
    public function signatures(): HasMany
    {
        return $this->hasMany(ESignature::class, 'document_type', 'document_type');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByDocumentType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('template_name');
    }

    public function scopeRequiresWitness($query)
    {
        return $query->where('requires_witness', true);
    }

    public function scopeRequiresNotarization($query)
    {
        return $query->where('requires_notarization', true);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getDocumentTypeTextAttribute(): string
    {
        return match ($this->document_type) {
            'lesson_plan' => 'Lesson Plan',
            'grade_submission' => 'Grade Submission',
            'monthly_report' => 'Monthly Report',
            'transcript' => 'Transcript',
            'admission_application' => 'Admission Application',
            default => ucfirst(str_replace('_', ' ', $this->document_type))
        };
    }

    public function getSignatureFieldsFormattedAttribute(): array
    {
        return $this->signature_fields ?? [];
    }

    public function getApprovalWorkflowFormattedAttribute(): array
    {
        return $this->approval_workflow ?? [];
    }

    public function getSignatureRequirementsFormattedAttribute(): array
    {
        return $this->signature_requirements ?? [];
    }

    public function getNotificationSettingsFormattedAttribute(): array
    {
        return $this->notification_settings ?? [];
    }

    public function getSecuritySettingsFormattedAttribute(): array
    {
        return $this->security_settings ?? [];
    }

    public function getExpiryDaysFormattedAttribute(): string
    {
        return $this->expiry_days . ' days';
    }

    public function getRequiresWitnessTextAttribute(): string
    {
        return $this->requires_witness ? 'Yes' : 'No';
    }

    public function getRequiresNotarizationTextAttribute(): string
    {
        return $this->requires_notarization ? 'Yes' : 'No';
    }

    public function getSignatureCountAttribute(): int
    {
        return $this->signatures()->count();
    }

    public function getActiveSignatureCountAttribute(): int
    {
        return $this->signatures()->whereIn('status', ['signed', 'verified'])->count();
    }

    public function getPendingSignatureCountAttribute(): int
    {
        return $this->signatures()->where('status', 'pending')->count();
    }

    // Methods
    public function generateTemplateCode(): string
    {
        $prefix = strtoupper(substr($this->document_type, 0, 3));
        $suffix = strtoupper(substr(str_replace(' ', '', $this->template_name), 0, 6));
        return $prefix . '-' . $suffix . '-' . strtoupper(uniqid());
    }

    public function activate(): bool
    {
        $this->update(['is_active' => true]);
        return true;
    }

    public function deactivate(): bool
    {
        $this->update(['is_active' => false]);
        return true;
    }

    public function canBeDeleted(): bool
    {
        return $this->signatures()->count() === 0;
    }

    public function canBeEdited(): bool
    {
        return $this->signatures()->whereIn('status', ['signed', 'verified'])->count() === 0;
    }

    public function getSignatureFieldsForDocument(): array
    {
        $fields = $this->signature_fields ?? [];
        $requirements = $this->signature_requirements ?? [];

        return array_map(function ($field) use ($requirements) {
            return [
                'name' => $field,
                'required' => in_array($field, $requirements['required_fields'] ?? []),
                'type' => $requirements['field_types'][$field] ?? 'text',
                'validation' => $requirements['field_validation'][$field] ?? []
            ];
        }, $fields);
    }

    public function getApprovalWorkflowSteps(): array
    {
        $workflow = $this->approval_workflow ?? [];
        return $workflow['steps'] ?? [];
    }

    public function getRequiredApprovers(): array
    {
        $workflow = $this->approval_workflow ?? [];
        return $workflow['approvers'] ?? [];
    }

    public function getApprovalLevels(): array
    {
        $workflow = $this->approval_workflow ?? [];
        return $workflow['levels'] ?? [];
    }

    public function getSignatureTypeRequirements(): array
    {
        $requirements = $this->signature_requirements ?? [];
        return $requirements['signature_types'] ?? [];
    }

    public function getSecurityLevel(): string
    {
        $security = $this->security_settings ?? [];
        return $security['level'] ?? 'standard';
    }

    public function getEncryptionSettings(): array
    {
        $security = $this->security_settings ?? [];
        return $security['encryption'] ?? [];
    }

    public function getAuditSettings(): array
    {
        $security = $this->security_settings ?? [];
        return $security['audit'] ?? [];
    }

    public function getNotificationRecipients(): array
    {
        $notifications = $this->notification_settings ?? [];
        return $notifications['recipients'] ?? [];
    }

    public function getNotificationTriggers(): array
    {
        $notifications = $this->notification_settings ?? [];
        return $notifications['triggers'] ?? [];
    }

    public function isFieldRequired(string $fieldName): bool
    {
        $fields = $this->getSignatureFieldsForDocument();
        $field = collect($fields)->firstWhere('name', $fieldName);
        return $field ? $field['required'] : false;
    }

    public function getFieldType(string $fieldName): string
    {
        $fields = $this->getSignatureFieldsForDocument();
        $field = collect($fields)->firstWhere('name', $fieldName);
        return $field ? $field['type'] : 'text';
    }

    public function validateSignatureData(array $data): array
    {
        $errors = [];
        $fields = $this->getSignatureFieldsForDocument();

        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $isRequired = $field['required'];
            $fieldType = $field['type'];

            if ($isRequired && empty($data[$fieldName])) {
                $errors[$fieldName] = "The {$fieldName} field is required.";
            }

            if (!empty($data[$fieldName])) {
                switch ($fieldType) {
                    case 'email':
                        if (!filter_var($data[$fieldName], FILTER_VALIDATE_EMAIL)) {
                            $errors[$fieldName] = "The {$fieldName} must be a valid email address.";
                        }
                        break;
                    case 'phone':
                        if (!preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $data[$fieldName])) {
                            $errors[$fieldName] = "The {$fieldName} must be a valid phone number.";
                        }
                        break;
                    case 'date':
                        if (!strtotime($data[$fieldName])) {
                            $errors[$fieldName] = "The {$fieldName} must be a valid date.";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (!$template->template_code) {
                $template->template_code = $template->generateTemplateCode();
            }
        });
    }
}