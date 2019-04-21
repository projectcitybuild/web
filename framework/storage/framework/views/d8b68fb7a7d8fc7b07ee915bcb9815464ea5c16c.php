<?php $__env->startSection('title', 'Login'); ?>
<?php $__env->startSection('description', 'Login to your Project City Build account to create forum posts, access personal player statistics and more.'); ?>

<?php $__env->startSection('contents'); ?>

    <div class="login">
        <div class="login__left">
            <h1>Sign In to PCB</h1>

            <small>
                <i class="fas fa-exclamation-circle"></i> 
                Notice: Anyone with a forum account before we moved to Discourse <a href="<?php echo e(route('front.password-reset')); ?>">must reset their password first</a>!
                <p />
                See <a href="https://forums.projectcitybuild.com/t/welcome-to-the-new-forums/32708/1" target="_blank">this post</a> for more details.
            </small>
            <br>

            <form method="post" action="<?php echo e(route('front.login.submit')); ?>">
                <?php echo csrf_field(); ?>
                
                <?php if($errors->any()): ?>
                    <div class="alert alert--error">
                        <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                        <?php echo e($errors->first()); ?>

                    </div>
                    <p>
                <?php endif; ?>

                <div class="form-row">
                    <input class="input-text <?php echo e($errors->any() ? 'input-text--error' : ''); ?>" name="email" type="email" placeholder="Email Address" value="<?php echo e(old('email')); ?>" />
                </div>
                <div class="form-row">
                    <input class="input-text <?php echo e($errors->any() ? 'input-text--error' : ''); ?>" name="password" type="password" placeholder="Password" />
                </div>
                <div class="form-row">
                    <button class="button button--large button--fill button--primary" type="submit">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>
                <div class="form-row">
                    <div class="login__options">
                        <div class="login__remember"></div>
                        <div class="login__forgot">
                            <a href="<?php echo e(route('front.password-reset')); ?>">Forgot your password?</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="login__right">
            <h1>Sign Up</h1>

            <div class="login__description">
                Members gain access to personal player statistics, the forums, in-game rank synchronization and more.
            </div>
            
            <a class="button button--fill button--large button--secondary" href="<?php echo e(route('front.register')); ?>">
                Create an Account
            </a>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/resources/views/front/pages/login/login.blade.php */ ?>