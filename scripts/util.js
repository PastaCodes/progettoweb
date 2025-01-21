export function formatPrice(number) {
    const parts = number.toFixed(2).split('.');
    const integerPart = parts[0];
    const decimalPart = parts[1];
    const withThousandSeparators = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return '&euro; ' + withThousandSeparators + ',' + decimalPart;
}


