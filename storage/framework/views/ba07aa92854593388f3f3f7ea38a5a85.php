

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Class</h1>
                <p class="text-gray-600 mt-2">Update class information</p>
            </div>
            <a href="<?php echo e(route('classes.show', $class)); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Class
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="<?php echo e(route('classes.update', $class)); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <!-- Class Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Class Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Class Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo e(old('name', $class->name)); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Session -->
                        <div>
                            <label for="session" class="block text-sm font-medium text-gray-700 mb-2">Session *</label>
                            <select id="session" name="session" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php $__errorArgs = ['session'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    required>
                                <?php $__currentLoopData = ['A','B','C','D','E','F']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($opt); ?>" <?php echo e(old('session', $class->session) === $opt ? 'selected' : ''); ?>><?php echo e($opt); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['session'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity *</label>
                            <input type="number" id="capacity" name="capacity" value="<?php echo e(old('capacity', $class->capacity)); ?>" min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   required>
                            <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Class Teacher -->
                        <div>
                            <label for="class_teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Class Teacher</label>
                            <select id="class_teacher_id" name="class_teacher_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php $__errorArgs = ['class_teacher_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Select a teacher</option>
                                <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($teacher->id); ?>" <?php echo e(old('class_teacher_id', $class->class_teacher_id) == $teacher->id ? 'selected' : ''); ?>>
                                        <?php echo e($teacher->user->name ?? 'Unnamed'); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['class_teacher_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Assign Teachers Dynamically -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Teachers</h3>
                    
                    <!-- Add Teacher Section -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <div class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label for="teacher_select" class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                                <select id="teacher_select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Choose a teacher...</option>
                                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($teacher->id); ?>" data-name="<?php echo e($teacher->user->name ?? 'Unnamed'); ?>">
                                            <?php echo e($teacher->user->name ?? 'Unnamed'); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label for="section_select" class="block text-sm font-medium text-gray-700 mb-2">Section/Subject</label>
                                <input type="text" id="section_select" placeholder="e.g., Mathematics, Science" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <button type="button" id="add_teacher_btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Add
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Teachers List -->
                    <div class="mb-4">
                        <h4 class="text-md font-medium text-gray-900 mb-2">Assigned Teachers</h4>
                        <div id="assigned_teachers_list" class="space-y-2">
                            <!-- Dynamically added teachers will appear here -->
                        </div>
                        <div id="no_teachers_message" class="text-gray-500 text-sm py-4 text-center border-2 border-dashed border-gray-200 rounded-lg">
                            No teachers assigned yet. Use the form above to add teachers.
                        </div>
                    </div>

                    <!-- Hidden inputs for form submission -->
                    <div id="hidden_inputs"></div>
                </div>

                <!-- Location Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Room Number -->
                        <div>
                            <label for="room_number" class="block text-sm font-medium text-gray-700 mb-2">Room Number</label>
                            <input type="text" id="room_number" name="room_number" value="<?php echo e(old('room_number', $class->room_number)); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Building -->
                        <div>
                            <label for="building" class="block text-sm font-medium text-gray-700 mb-2">Building</label>
                            <input type="text" id="building" name="building" value="<?php echo e(old('building', $class->building)); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Floor -->
                        <div>
                            <label for="floor" class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                            <input type="text" id="floor" name="floor" value="<?php echo e(old('floor', $class->floor)); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Wing -->
                        <div>
                            <label for="wing" class="block text-sm font-medium text-gray-700 mb-2">Wing</label>
                            <input type="text" id="wing" name="wing" value="<?php echo e(old('wing', $class->wing)); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo e(old('description', $class->description)); ?></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="<?php echo e(route('classes.show', $class)); ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const teacherSelect = document.getElementById('teacher_select');
    const sectionInput = document.getElementById('section_select');
    const addBtn = document.getElementById('add_teacher_btn');
    const assignedList = document.getElementById('assigned_teachers_list');
    const noTeachersMessage = document.getElementById('no_teachers_message');
    const hiddenInputs = document.getElementById('hidden_inputs');
    
    let assignedTeachers = [];
    let teacherCounter = 0;

    // Load existing assigned teachers
    <?php if(isset($assignedTeachers) && count($assignedTeachers) > 0): ?>
        <?php $__currentLoopData = $assignedTeachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacherId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $teacher = $teachers->firstWhere('id', $teacherId);
            ?>
            <?php if($teacher): ?>
                assignedTeachers.push({
                    teacherId: '<?php echo e($teacher->id); ?>',
                    teacherName: '<?php echo e($teacher->user->name ?? 'Unnamed'); ?>',
                    section: 'General',
                    counter: teacherCounter++
                });
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        updateTeachersList();
    <?php endif; ?>

    // Add teacher to list
    addBtn.addEventListener('click', function() {
        const teacherId = teacherSelect.value;
        const teacherName = teacherSelect.options[teacherSelect.selectedIndex].dataset.name;
        const section = sectionInput.value.trim();

        // Validation
        if (!teacherId) {
            alert('Please select a teacher.');
            return;
        }

        if (!section) {
            alert('Please enter a section/subject.');
            return;
        }

        // Check if teacher is already assigned
        if (assignedTeachers.some(t => t.teacherId === teacherId)) {
            alert('This teacher is already assigned to this class.');
            return;
        }

        // Add to assigned teachers array
        const teacherData = {
            teacherId: teacherId,
            teacherName: teacherName,
            section: section,
            counter: teacherCounter++
        };
        assignedTeachers.push(teacherData);

        updateTeachersList();

        // Clear form
        teacherSelect.value = '';
        sectionInput.value = '';
    });

    // Remove teacher from list
    assignedList.addEventListener('click', function(e) {
        if (e.target.closest('.remove-teacher')) {
            const button = e.target.closest('.remove-teacher');
            const counter = parseInt(button.dataset.counter);
            
            // Remove from array
            assignedTeachers = assignedTeachers.filter(t => t.counter !== counter);
            
            updateTeachersList();
        }
    });

    // Update teachers list display
    function updateTeachersList() {
        // Clear existing list
        assignedList.innerHTML = '';
        
        if (assignedTeachers.length === 0) {
            noTeachersMessage.style.display = 'block';
        } else {
            noTeachersMessage.style.display = 'none';
            
            assignedTeachers.forEach(teacher => {
                const listItem = document.createElement('div');
                listItem.className = 'flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg';
                listItem.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-xs font-medium text-white">${teacher.teacherName.charAt(0)}</span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">${teacher.teacherName}</div>
                            <div class="text-xs text-gray-500">${teacher.section}</div>
                        </div>
                    </div>
                    <button type="button" class="text-red-600 hover:text-red-800 remove-teacher" data-counter="${teacher.counter}">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                assignedList.appendChild(listItem);
            });
        }
        
        updateHiddenInputs();
    }

    // Update hidden inputs for form submission
    function updateHiddenInputs() {
        // Clear existing hidden inputs
        hiddenInputs.innerHTML = '';
        
        // Add hidden inputs for each assigned teacher
        assignedTeachers.forEach(teacher => {
            const teacherInput = document.createElement('input');
            teacherInput.type = 'hidden';
            teacherInput.name = 'teachers[]';
            teacherInput.value = teacher.teacherId;
            hiddenInputs.appendChild(teacherInput);
        });
    }

    // Handle form submission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (assignedTeachers.length === 0) {
            e.preventDefault();
            alert('Please assign at least one teacher to this class.');
            return;
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/classes/edit.blade.php ENDPATH**/ ?>