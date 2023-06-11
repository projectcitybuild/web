import React, {ReactNode} from "react";

type Props = {
  label: String,
  errorText?: String,
  supportText?: String,
  children: ReactNode,
}

export default function FormField(props: Props) {
  return (
    <div className="field">
      <label className="label">{props.label}</label>

      {props.children}

      { props.errorText && props.errorText != "" &&
        (<p className="help is-danger">{props.errorText}</p>)
      }
      { props.supportText && props.supportText != "" &&
        (<p className="help">{props.supportText}</p>)
      }
    </div>
  )
}