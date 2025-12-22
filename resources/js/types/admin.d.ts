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

export interface DashboardStats {
    countOrders: number;
    getTotalRevenue: number;
    countItems: number;
    countCustomers: number;
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

export interface Delivery {
    id: number;
    order_id: number;
    status: 'scheduled' | 'rescheduled' | 'in_transit' | 'delivered' | 'failed';
    scheduled_date: string;
    actual_delivery_date: string | null;
    driver_name: string | null;
    remarks: string | null;
    proof_of_delivery: string | null;
    created_at: string;
    updated_at: string;
    order?: Order;
}

export interface ReportOverview {
    sales_summary: {
        total_orders: number;
        total_revenue: number;
        average_order_value: number;
    };
    order_summary: {
        total_orders: number;
        pending_orders: number;
        confirmed_orders: number;
        paid_orders: number;
        assembled_orders: number;
        shipped_orders: number;
        delivered_orders: number;
        cancelled_orders: number;
    };
    billing_summary: {
        total_billings: number;
        total_amount: number;
        paid_amount: number;
        unpaid_amount: number;
        paid_count: number;
        unpaid_count: number;
    };
    delivery_summary: {
        total_deliveries: number;
        pending_deliveries: number;
        in_transit_deliveries: number;
        delivered_deliveries: number;
        with_proof: number;
    };
    inventory_summary: {
        total_items: number;
        total_stock_value: number;
        low_stock_count: number;
    };
}

export interface SalesMetrics {
    summary: {
        total_orders: number;
        total_revenue: number;
        average_order_value: number;
    };
    by_period: Array<{
        period: string;
        order_count: number;
        revenue: number;
    }>;
    top_items: Array<{
        id: number;
        item_name: string;
        item_code: string;
        total_quantity: number;
        total_revenue: number;
    }>;
    by_payment_method: Array<{
        payment_method: string;
        order_count: number;
        revenue: number;
    }>;
}

export interface InventoryMetrics {
    stock_levels: Array<{
        id: number;
        item_code: string;
        item_name: string;
        quantity: number;
        price: number;
        stock_value: number;
        supplier_name: string | null;
    }>;
    low_stock_items: InventoryItem[];
    damaged_summary: {
        total_records: number;
        total_damaged_quantity: number;
        estimated_loss: number;
    };
    by_supplier: Array<{
        id: number;
        supplier_name: string;
        item_count: number;
        total_quantity: number;
        total_value: number;
    }>;
    total_stock_value: number;
    total_items: number;
}

export interface OrderMetrics {
    by_status: Array<{
        status: string;
        count: number;
        total_amount: number;
    }>;
    by_delivery_method: Array<{
        delivery_method: string;
        count: number;
        total_amount: number;
    }>;
    fulfillment: {
        total_orders: number;
        pending_orders: number;
        confirmed_orders: number;
        paid_orders: number;
        assembled_orders: number;
        shipped_orders: number;
        delivered_orders: number;
        cancelled_orders: number;
    };
}

export interface BillingMetrics {
    summary: {
        total_billings: number;
        total_amount: number;
        paid_amount: number;
        unpaid_amount: number;
        paid_count: number;
        unpaid_count: number;
    };
    outstanding_payments: Billing[];
}

export interface DeliveryMetrics {
    status_overview: Array<{
        status: string;
        count: number;
    }>;
    performance: {
        total_deliveries: number;
        pending_deliveries: number;
        in_transit_deliveries: number;
        delivered_deliveries: number;
        with_proof: number;
    };
}
