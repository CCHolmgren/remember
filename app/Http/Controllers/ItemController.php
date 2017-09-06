<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    function index()
    {
        return [
            'items' => request()->user()->items
        ];
    }

    function update(Item $item)
    {
        abort_if($item->user_id !== request()->user()->id, 403);

        $data = [
            'name' => request('name'),
            'type' => request('type'),
            'content' => collect(request()->only('content')['content'])->reject(function ($item) {
                return array_get($item, 'key') == null || trim(array_get($item, 'key')) == "";
            })->all()
        ];
        $item->fill($data)->save();

        return ['item' => $item->refresh()];
    }

    function delete(Item $item)
    {
        abort_if($item->user_id !== request()->user()->id, 403);

        $item->delete();
    }

    function save()
    {
        $data = [
            'name' => request('name'),
            'type' => request('type'),
            'user_id' => request()->user()->id,
            'content' => collect(request()->only('content')['content'])->reject(function($item) {
                return array_get($item, 'key') == null || trim(array_get($item, 'key')) == "";
            })->all()
        ];

        return [
            'item' => Item::create($data)
        ];
    }

    function copy(Item $item)
    {
        $item = $item->replicate();
        $item->save();

        return [
            'item' => $item
        ];
    }
}