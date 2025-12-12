export interface DataTableColumn<T = any> {
    label: string;
    key: keyof T | string;
    align?: 'left' | 'center' | 'right';
    render?: (value: any, row: T) => string | number;
    class?: string;
    sortable?: boolean;
}

export interface DataTableAction<T = any> {
    label: string;
    icon: any;
    onClick: (row: T) => void;
    variant?: 'default' | 'ghost' | 'destructive' | 'outline';
    show?: (row: T) => boolean;
    class?: string;
}

export interface InventoryItem {
    id: number;
    item_name: string;
    item_code: string;
    brand_name: string;
    category: string;
    supplier_id?: number;
    unit_price: number;
    quantity: number;
    restock_threshold: number;
    description?: string;
    item_image_1?: string;
    item_image_2?: string;
    item_image_3?: string;
    created_at: string;
    updated_at: string;
}

export interface Supplier {
    id: number;
    supplier_name: string;
    email?: string;
    phone?: string;
    address?: string;
    status?: string;
    created_at: string;
    updated_at: string;
}

export interface DamagedItem {
    id: number;
    item_id: number;
    quantity: number;
    discount: number | null;
    status: 'damaged' | 'resellable' | 'disposed';
    remarks: string | null;
    created_at: string;
    updated_at: string;
    item: InventoryItem;  // The related item (eager loaded)
}
