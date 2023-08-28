import { faQuestionCircle } from "@fortawesome/free-regular-svg-icons"
import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faArrowPointer,
  faArrowRight, faCaretDown, faCheck,
  faChevronLeft,
  faChevronRight,
  faEnvelope,
  faExternalLink,
  faLock, faMobilePhone, faMobileScreenButton,
  faUser,
} from "@fortawesome/free-solid-svg-icons";
import {
  faDiscord,
} from "@fortawesome/free-brands-svg-icons";
import { bool } from "yup"

export enum IconToken {
  arrowPointer,
  arrowRight,
  caretDown,
  check,
  chevronLeft,
  chevronRight,
  discord,
  envelope,
  externalLink,
  lock,
  mobile,
  question,
  user,
}

const iconFrom = (token: IconToken) => {
  switch (token) {
    case IconToken.arrowPointer: return faArrowPointer
    case IconToken.arrowRight: return faArrowRight
    case IconToken.caretDown: return faCaretDown
    case IconToken.check: return faCheck
    case IconToken.chevronLeft: return faChevronLeft
    case IconToken.chevronRight: return faChevronRight
    case IconToken.discord: return faDiscord
    case IconToken.envelope: return faEnvelope
    case IconToken.externalLink: return faExternalLink
    case IconToken.lock: return faLock
    case IconToken.mobile: return faMobileScreenButton
    case IconToken.question: return faQuestionCircle
    case IconToken.user: return faUser
  }
}

export default function Icon(props: {
  token: IconToken
}) {

  return (
    <FontAwesomeIcon icon={iconFrom(props.token)} />
  )
}