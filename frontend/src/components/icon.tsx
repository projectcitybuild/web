import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faArrowPointer,
  faEnvelope,
  faLock,
  faUser,
} from "@fortawesome/free-solid-svg-icons";
import {
  faDiscord,
} from "@fortawesome/free-brands-svg-icons";

export enum IconToken {
  arrowPointer,
  discord,
  envelope,
  lock,
  user,
}

const iconFrom = (token: IconToken) => {
  switch (token) {
    case IconToken.arrowPointer: return faArrowPointer
    case IconToken.discord: return faDiscord
    case IconToken.envelope: return faEnvelope
    case IconToken.lock: return faLock
    case IconToken.user: return faUser
  }
}

type Props = {
  token: IconToken,
}

export default function Icon(props: Props) {
  return (
    <FontAwesomeIcon icon={iconFrom(props.token)} />
  )
}