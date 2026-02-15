import { Svg } from "./Components/SvgIcon.vue"

export const Icons: Record<string, Svg> = {
    plus: {
        paths: [
            {
                d: 'M12 4.5v15m7.5-7.5h-15',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    bin: {
        paths: [
            {
                d: 'm14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    chevronDown: {
        paths: [
            {
                d: 'm19.5 8.25-7.5 7.5-7.5-7.5',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    chevronUp: {
        paths: [
            {
                d: 'm4.5 15.75 7.5-7.5 7.5 7.5',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    cloudPush: {
        paths: [
            {
                d: 'M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    pencil: {
        paths: [
            {
                d: 'm16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    filter: {
        paths: [
            {
                d: 'M6 13.5V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 9.75V10.5',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    refresh: {
        paths: [
            {
                d: 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    email: {
        paths: [
            {
                d: 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    close: {
        paths: [
            {
                d: 'M6 18 18 6M6 6l12 12',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    unlock: {
        paths: [
            {
                d: 'M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    lock: {
        paths: [
            {
                d: 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    checkShield: {
        paths: [
            {
                d: 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    check: {
        paths: [
            {
                d: 'm4.5 12.75 6 6 9-13.5',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    arrowLeft: {
        paths: [
            {
                d: 'M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    alert: {
        paths: [
            {
                d: 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    clock: {
        paths: [
            {
                d: 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    eye: {
        paths: [
            {
                d: 'M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    users: {
        paths: [
            {
                d: 'M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    moderation: {
        paths: [
            {
                d: 'M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
            {
                d: 'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    cube: {
        fill: 'currentColor',
        paths: [
            {
                d: 'M9.98189 4.50602c1.24881-.67469 2.78741-.67469 4.03621 0l3.9638 2.14148c.3634.19632.6862.44109.9612.72273l-6.9288 3.60207L5.20654 7.225c.2403-.22108.51215-.41573.81157-.5775l3.96378-2.14148ZM4.16678 8.84364C4.05757 9.18783 4 9.5493 4 9.91844v4.28296c0 1.3494.7693 2.5963 2.01811 3.2709l3.96378 2.1415c.32051.1732.66011.3019 1.00901.3862v-7.4L4.16678 8.84364ZM13.009 20c.3489-.0843.6886-.213 1.0091-.3862l3.9638-2.1415C19.2307 16.7977 20 15.5508 20 14.2014V9.91844c0-.30001-.038-.59496-.1109-.87967L13.009 12.6155V20Z',
            },
        ],
    },
    grid: {
        paths: [
            {
                d: 'M9.143 4H4.857A.857.857 0 0 0 4 4.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 10 9.143V4.857A.857.857 0 0 0 9.143 4Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 20 9.143V4.857A.857.857 0 0 0 19.143 4Zm-10 10H4.857a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286A.857.857 0 0 0 9.143 14Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286a.857.857 0 0 0-.857-.857Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    servers: {
        paths: [
            {
                d: 'M5 12a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1M5 12h14M5 12a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1m-2 3h.01M14 15h.01M17 9h.01M14 9h.01',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
    cashier: {
        paths: [
            {
                d: 'M5 18h14M5 18v3h14v-3M5 18l1-9h12l1 9M16 6v3m-4-3v3m-2-6h8v3h-8V3Zm-1 9h.01v.01H9V12Zm3 0h.01v.01H12V12Zm3 0h.01v.01H15V12Zm-6 3h.01v.01H9V15Zm3 0h.01v.01H12V15Zm3 0h.01v.01H15V15Z',
                strokeLineCap: 'round',
                strokeLineJoin: 'round',
            },
        ],
    },
}

