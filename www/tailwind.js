module.exports = {
    theme: {
        extend: {
            spacing: {
                "8": "2rem",
                "72": "18rem",
                "80": "20rem",
                "88": "22rem",
                "96": "24rem",
                "104": "26rem",
                "112": "28rem",
                "120": "30rem",
                "128": "32rem",
                "136": "34rem",
                "144": "36rem",
                "152": "38rem",
                "160": "40rem"
            },
            opacity: {
                "10": ".1",
                "20": ".2",
                "30": ".3",
                "40": ".4",
                "50": ".5",
                "60": ".6",
                "70": ".7",
                "80": ".8",
                "90": ".9"
            },
            container: {
                center: true
            }
        }
    },
    variants: { 
        // https://tailwindcss.com/docs/configuring-variants#default-variants-reference
        margin: ['responsive', 'last', 'first'],
        borderRadius: ['responsive', 'last', 'first'],
        fill: ['responsive', 'hover', 'focus'],
        borderColor: ['responsive', 'hover', 'focus'],
        borderStyle: ['responsive', 'last', 'first'],
        borderWidth: ['responsive', 'last', 'first'],
        padding: ['responsive', 'last', 'first'],
    },
    plugins: []
};
