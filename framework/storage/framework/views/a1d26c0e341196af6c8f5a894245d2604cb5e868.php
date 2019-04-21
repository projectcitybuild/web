<?php /* /var/www/resources/views/front/components/server-feed.blade.php */ ?>
<section class="server-feed">
    <?php $__currentLoopData = $serverCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="category">
        <h5 class="category__heading"><?php echo e($category->name); ?></h5>
        <?php $__currentLoopData = $category->servers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $server): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="server <?php echo e($server->isOnline() ? 'server--online' : 'server--offline'); ?>">
            <div class="server__title"><?php echo e($server->name); ?></div>
            <div class="server__players badge <?php echo e($server->isOnline() ? 'badge--secondary' : 'badge--light'); ?>"><?php echo e($server->isOnline() ? $server->status->num_of_players.'/'.$server->status->num_of_slots : 'Offline'); ?></div>
            <div class="server__ip"><?php echo e($server->ip_alias ?: $server->getAddress()); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</section>