import styles from './filled-button.module.scss';

type Props = {
  text: string,
  disabled?: boolean|undefined,
  loading: boolean,
  submit?: boolean|undefined,
  onClick?: () => Promise<void>|undefined,
}

export default function FilledButton(props: Props) {
  return (
    <button
      type={props.submit ? "submit" : "button"}
      className={`button is-fullwidth ${props.loading ? "is-loading" : null} ${styles.button}`}
      disabled={props.disabled}
    >
      {props.text}
    </button>
  )
}