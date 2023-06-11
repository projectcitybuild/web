import React, {ReactNode} from "react";

type Props = {
  label?: string | undefined,
  errorText?: string | undefined,
  supportText?: string | undefined,
  className?: string | undefined;
  children: ReactNode,
}

export default function FormField(props: Props) {
  return (
    <div className={`field ${props.className}`}>
      { props.label &&
        (<label className="label">{props.label}</label>)
      }

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
