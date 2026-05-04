<?php

namespace Dustov\Quotes\Http\Controllers;

use Dustov\Quotes\QuotesManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class QuoteController extends Controller
{

    protected QuotesManager $manager;

    public function __construct(QuotesManager $manager)
    {
        $this->manager = $manager;
    }

    public function show(int $id)
    {
        try {
            $quote = $this->manager->getQuote($id);
            return response()->json($quote);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * show quotes in cache
     */
    public function index(Request $request)
    {
        return response()->json(
            $this->manager->paginateQuotes(
                $request->integer('page', 1),
                $request->integer('per_page', 10)
            )
        );
    }
}
