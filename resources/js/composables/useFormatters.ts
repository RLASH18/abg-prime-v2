export function useFormatters() {
    // Format number as Philippine Peso currency
    const formatCurrency = (amount: number): string => {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
        }).format(amount);
    };

    // Format date string to readable format
    const formatDate = (dateString: string): string => {
        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            hour12: true,
        }).format(new Date(dateString));
    };

    return {
        formatCurrency,
        formatDate,
    };
}
