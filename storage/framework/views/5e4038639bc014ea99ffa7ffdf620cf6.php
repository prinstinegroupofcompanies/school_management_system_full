

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Study Materials</h1>
        <?php if(auth()->user()->user_type === 'teacher'): ?>
        <a href="<?php echo e(route('study-materials.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Upload Material
        </a>
        <?php endif; ?>
    </div>

    <!-- Subject Filter -->
    <?php if($subjects->count() > 0): ?>
    <div class="mb-6 bg-white shadow rounded-lg p-4">
        <h3 class="text-lg font-medium text-gray-900 mb-3">Filter by Subject</h3>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('study-materials.index')); ?>" 
               class="px-3 py-1 rounded-full text-sm <?php echo e(!$subject ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
                All Subjects
            </a>
            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('study-materials.index', ['subject' => $subj->id])); ?>" 
                   class="px-3 py-1 rounded-full text-sm <?php echo e($subject && $subject->id == $subj->id ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
                    <?php echo e($subj->name); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if($materials->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-<?php echo e($material->type_color); ?>-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-<?php echo e($material->type_color); ?>-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?php echo e($material->type_color); ?>-100 text-<?php echo e($material->type_color); ?>-800">
                                    <?php echo e(ucfirst($material->type)); ?>

                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500"><?php echo e($material->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-2"><?php echo e($material->title); ?></h3>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?php echo e(Str::limit($material->description, 100)); ?></p>
                    
                    <div class="space-y-2 text-sm text-gray-500">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <?php echo e($material->subject->name); ?>

                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <?php echo e($material->class->name); ?>

                        </div>
                        <?php if($material->file_path): ?>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <?php echo e($material->file_name); ?> (<?php echo e($material->file_size_formatted); ?>)
                        </div>
                        <?php endif; ?>
                        <?php if($material->tags && count($material->tags) > 0): ?>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <div class="flex flex-wrap gap-1">
                                <?php $__currentLoopData = array_slice($material->tags, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded"><?php echo e($tag); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($material->tags) > 3): ?>
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">+<?php echo e(count($material->tags) - 3); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex space-x-2">
                            <a href="<?php echo e(route('study-materials.show', $material)); ?>" 
                               class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</a>
                            <?php if(auth()->user()->user_type === 'teacher' && $material->teacher_id === auth()->id()): ?>
                                <a href="<?php echo e(route('study-materials.edit', $material)); ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                            <?php endif; ?>
                        </div>
                        <?php if($material->isDownloadable()): ?>
                            <a href="<?php echo e($material->download_url); ?>" 
                               class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="mt-8">
            <?php echo e($materials->links()); ?>

        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No study materials</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by uploading your first study material.</p>
            <?php if(auth()->user()->user_type === 'teacher'): ?>
            <div class="mt-6">
                <a href="<?php echo e(route('study-materials.create')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Upload Material
                </a>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\DELL\SchoolManagementSystem\resources\views/study-materials/index.blade.php ENDPATH**/ ?>