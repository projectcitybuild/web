<?php /* /var/www/resources/views/front/pages/home.blade.php */ ?>
<?php $__env->startSection('title', 'Project City Build - Creative Gaming Community'); ?>
<?php $__env->startSection('description', 'Medium size Minecraft community active since 2010. We host multiple servers and maps, including Survival, Creative, as well as maps dedicated to projects such as our signature Big City map. With over 14,000 registrants, PCB has a bit of something for everyone.'); ?>
<?php $__env->startSection('header_style', 'hero'); ?>

<?php $__env->startSection('left'); ?>
    <div id="announcements"></div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.sidebar-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>