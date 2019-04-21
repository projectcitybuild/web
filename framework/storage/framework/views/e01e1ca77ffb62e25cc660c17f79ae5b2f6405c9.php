<?php $__env->startSection('title', 'Create an Account'); ?>
<?php $__env->startSection('description', 'Create a PCB account to create forum posts, access personal player statistics and more.'); ?>

<?php $__env->startPush('head'); ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function submitForm() {
            document.getElementById('form').submit();
        }
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('contents'); ?>

    <div class="card card--narrow card--centered">
        <div class="card__body card__body--padded">
            <h1>Create an Account</h1>

            <form method="post" action="<?php echo e(route('front.register.submit')); ?>" id="form">
                <?php echo csrf_field(); ?>
                
                <?php if($errors->any()): ?>
                    <div class="alert alert--error">
                        <h3><i class="fas fa-exclamation-circle"></i> Error</h3>
                        <?php echo e($errors->first()); ?>

                    </div>
                    <p>
                <?php endif; ?>

                <div class="form-row">
                    <label>Email Address</label>
                    <input class="input-text <?php echo e($errors->any() ? 'input-text--error' : ''); ?>" name="email" type="email" placeholder="Email Address" value="<?php echo e(old('email')); ?>" />
                </div>
                <div class="form-row">
                    <label>Password</label>
                    <input class="input-text <?php echo e($errors->any() ? 'input-text--error' : ''); ?>" name="password" type="password" placeholder="Password" />
                </div>
                <div class="form-row">
                    <input class="input-text <?php echo e($errors->any() ? 'input-text--error' : ''); ?>" name="password_confirm" type="password" placeholder="Password (Confirm)" />
                </div>

                <div class="form-row">
                    <button
                        class="g-recaptcha button button--large button--fill button--primary"
                        data-sitekey="@recaptcha_key"
                        data-callback="submitForm"
                        >
                        <i class="fas fa-chevron-right"></i> Register
                    </button>
                </div>
            </form>

            <div class="register__fineprint">
                By signing up, you agree to our community <a href="<?php echo e(route('terms')); ?>">terms and conditions</a>.
            </div>

        </div>

    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/resources/views/front/pages/register/register.blade.php */ ?>