export interface Quote {
    id: number;
    quote: string;
    author: string;
}

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}

export interface QuoteResponse{
    data: Quote[],
    meta: PaginationMeta
}
