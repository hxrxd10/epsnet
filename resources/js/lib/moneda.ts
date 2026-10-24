const QUETZALES = new Intl.NumberFormat('es-GT', {
    style: 'currency',
    currency: 'GTQ',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

/** «Q1,250.00»: un monto en quetzales. */
export const formatearQuetzales = (valor: number | string): string =>
    QUETZALES.format(Number(valor));
