<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Customer;
use App\Utils\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CustomerController extends BaseController
{
    private $rules = [
        'Nom' => 'required',
        'Titre' => 'required',
        'Description' => 'required',
        'Logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    public function __construct()
    {
        $this->authorizeResource(Customer::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $customer = Customer::orderBy('id', 'desc')->get();
        return $this->handleResponse($customer, 'Customers have been retrieved!');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules);
        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 302);
        }

        $image = Utils::store_image($request, $input['Titre'], 'customer', 'Logo');

        $customer = Customer::create([
            'name' => $request->Nom,
            'title' => $request->Titre,
            'icon' => $request->Icon,
            'content' => $request->Description,
            'logo' => $image,
        ]);
        return $this->handleResponse($customer, 'Customer created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Customer $customer): JsonResponse
    {
        return $this->handleResponse($customer, 'Customer retrieved.');
    }


    public function update(Request $request, Customer $customer): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'Nom' => 'required',
            'Titre' => 'required',
            'Description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $image = $customer->logo;
        if ($request->file('Logo')) {
            $image = Utils::store_image($request, $input['Titre'], 'customer', 'Logo');
        }

        $customer->update([
            'name' => $request->Nom,
            'title' => $request->Titre,
            'icon' => $request->Icon,
            'content' => $request->Description,
            'logo' => $image,
        ]);

        return $this->handleResponse($customer, 'Customer successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return $this->handleResponse([], $customer->title . ' deleted!');
    }
}
