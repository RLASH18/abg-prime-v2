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
    image_path?: string;
}
