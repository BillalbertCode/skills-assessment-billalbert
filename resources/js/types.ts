export interface Quote {
    id: number;
    quote: string;
    author: string;
}

export interface LaravelPaginator {
    data: Quote[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}
