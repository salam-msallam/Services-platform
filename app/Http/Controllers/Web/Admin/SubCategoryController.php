<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubCategoryRequest;
use App\Http\Requests\Admin\UpdateSubCategoryRequest;
use App\Models\SubCategory;
use App\Services\Admin\CategoryService;
use App\Services\Admin\SubCategoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SubCategoryController extends Controller
{
    public function __construct(
        protected SubCategoryService $subCategoryService,
        protected CategoryService $categoryService,
    ) {}

    public function index(): View
    {
        $subCategories = $this->subCategoryService->listSubCategories();

        return view('admin.sub-categories.index', compact('subCategories'));
    }

    public function create(): View
    {
        $categories = $this->categoryService->listCategories();

        return view('admin.sub-categories.create', compact('categories'));
    }

    public function show(SubCategory $subCategory): View
    {
        $subCategory->load('category');

        return view('admin.sub-categories.show', compact('subCategory'));
    }

    public function edit(SubCategory $subCategory): View
    {
        $categories = $this->categoryService->listCategories();

        return view('admin.sub-categories.edit', compact('subCategory', 'categories'));
    }

    public function store(StoreSubCategoryRequest $request): RedirectResponse
    {
        $this->subCategoryService->createSubCategory($request->validated());

        return redirect()
            ->route('admin.sub-categories.index')
            ->with('success', __('admin.sub_category_created'));
    }

    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory): RedirectResponse
    {
        $this->subCategoryService->updateSubCategory($subCategory, $request->validated());

        return redirect()
            ->route('admin.sub-categories.index')
            ->with('success', __('admin.sub_category_updated'));
    }

    public function destroy(SubCategory $subCategory): RedirectResponse
    {
        $this->subCategoryService->deleteSubCategory($subCategory);

        return redirect()
            ->route('admin.sub-categories.index')
            ->with('success', __('admin.sub_category_deleted'));
    }
}
