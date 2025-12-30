export interface Product {
    id: number;
    item_code: string;
    item_name: string;
    brand_name: string;
    category: string;
    unit_price: number;
    quantity: number;
    restock_threshold: number;
    description?: string;
    item_image_1?: string;
    item_image_2?: string;
    item_image_3?: string;
}
