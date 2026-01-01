import { ref } from 'vue';

export function useImagePreviews(imageCount: number = 3) {
    const imagePreviews = ref<(string | null)[]>(Array(imageCount).fill(null));

    const handleImageSelect = (event: Event, index: number): void => {
        const input = event.target as HTMLInputElement;
        const file = input.files?.[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreviews.value[index] = e.target?.result as string;
            };
            reader.readAsDataURL(file);
        }
    };

    const removeImage = (index: number, inputId: string): void => {
        imagePreviews.value[index] = null;
        const input = document.getElementById(inputId) as HTMLInputElement;
        if (input) {
            input.value = '';
        }
    };

    const setInitialPreviews = (initialImages: (string | null)[]): void => {
        imagePreviews.value = [...initialImages];
    };

    const clearAllPreviews = (): void => {
        imagePreviews.value = Array(imageCount).fill(null);
    };

    const hasAnyImage = (): boolean => {
        return imagePreviews.value.some((preview) => preview !== null);
    };

    return {
        imagePreviews,
        handleImageSelect,
        removeImage,
        setInitialPreviews,
        clearAllPreviews,
        hasAnyImage,
    };
}
