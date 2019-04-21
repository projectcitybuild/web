<?php /* /var/www/resources/views/front/layouts/sidebar-layout.blade.php */ ?>
<?php $__env->startSection('contents'); ?>
    <div class="contents__body">
        <?php echo $__env->yieldContent('left'); ?>
    </div>

    <div class="contents__sidebar">
        <?php echo $__env->make('front.components.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>    
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>