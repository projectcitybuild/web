export type ButtonVariant = 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger' | 'link'
export type ButtonSize = 'sm' | 'md' | 'lg'

const variantClasses: Record<ButtonVariant, string> = {
    primary: 'bg-primary text-primary-foreground hover:bg-primary-hover active:bg-primary-active',
    secondary: 'bg-surface-muted text-foreground hover:bg-surface-hover active:bg-surface-hover',
    outline: 'border border-border-strong bg-surface text-foreground hover:bg-surface-muted active:bg-surface-hover',
    ghost: 'bg-transparent text-foreground hover:bg-surface-muted active:bg-surface-hover',
    danger: 'bg-danger text-danger-foreground hover:bg-danger-hover active:bg-danger-hover',
    link: 'bg-transparent text-primary underline-offset-4 hover:underline active:text-primary-active',
}

const sizeClasses: Record<ButtonSize, string> = {
    sm: 'min-h-8 px-3 text-xs',
    md: 'min-h-10 px-4 text-sm',
    lg: 'min-h-12 px-5 text-base',
}

const iconSizeClasses: Record<ButtonSize, string> = {
    sm: 'size-8 p-0',
    md: 'size-10 p-0',
    lg: 'size-12 p-0',
}

export function getButtonClasses(variant: ButtonVariant, size: ButtonSize, iconOnly = false): string[] {
    return [
        'inline-flex items-center justify-center gap-2 rounded-[var(--radius-md)] font-medium transition-colors motion-reduce:transition-none',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface',
        'disabled:cursor-not-allowed disabled:border-disabled disabled:bg-disabled disabled:text-disabled-foreground disabled:hover:bg-disabled disabled:active:bg-disabled',
        iconOnly ? iconSizeClasses[size] : sizeClasses[size],
        variantClasses[variant],
    ]
}
