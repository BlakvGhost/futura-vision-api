<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ContactController extends BaseController
{
    private $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required',
        'message' => 'required',
    ];

    public function __construct()
    {
        $this->authorizeResource(Contact::class);
    }
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $contact = Contact::orderBy('id', 'desc')->get();
        return $this->handleResponse($contact, 'Contact have been retrieved!');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->post();

        $validator = Validator::make($input, $this->rules);
        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 302);
        }

        $contact = Contact::create($input);
        return $this->handleResponse($contact, 'Contact created!');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function show(Contact $contact): JsonResponse
    {
        return $this->handleResponse($contact, 'Contact retrieved.');
    }


    public function update(Request $request, Contact $contact): JsonResponse
    {
        $input = $request->post();

        $validator = Validator::make($input, $this->rules);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $contact->update($input);

        return $this->handleResponse($contact, 'Contact successfully updated!');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return $this->handleResponse([], $contact->name . ' deleted!');
    }
}
