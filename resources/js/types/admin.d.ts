import { User } from ".";

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

export interface OrderItem {
    id: number;
    order_id: number;
    item_id: number;
    quantity: number;
    unit_price: number;
    item?: InventoryItem;
}

export interface Order {
    id: number;
    user_id: number;
    status: 'pending' | 'confirmed' | 'assembled' | 'shipped' | 'delivered' | 'paid' | 'cancelled';
    payment_method: 'cash' | 'gcash' | 'bank_transfer';
    total_amount: number;
    delivery_method: 'pickup' | 'delivery';
    delivery_address: string | null;
    created_at: string;
    updated_at: string;
    user?: User;
    order_items?: OrderItem[];
}

export interface Billing {
    id: number;
    order_id: number;
    billing_number: string;
    amount: number;
    status: 'unpaid' | 'paid' | 'cancelled';
    paid_at: string | null;
    created_at: string;
    updated_at: string;
    order?: Order;
}
