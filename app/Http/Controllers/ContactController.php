<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    protected $model;
    public function __construct(Contact $contact)
    {
        $this->model = $contact;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $resultsPerPage = 10;
        $query = $this->model->query();

        if ($request->has('data_inicial') && $request->has('data_final')) {
            $query->whereBetween('created_at', [$request->input('data_inicial'), $request->input('data_final')]);
        }

        $data = $query->paginate($resultsPerPage);
        return response($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {    
        $cpf = preg_replace("/[^0-9]/", "", $request->input('cpf'));
        if (strlen($cpf) != 11 || !is_numeric($cpf)) {
            return response()->json(['error' => 'CPF inválido'], 422);
        }
        try {
            $this->model->create($request->all());
            return response()->json(['message' => 'Cadastro realizado com sucesso!']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            return response('Contact not found');
        }
        return response(['contact' => $contact]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            return response(['errors' => 'Contato não encontrado'], 404);
        }

        $contact->fill($request->all());

        if ($contact->save()) {
            return response(['message' => 'Contato atualizado com sucesso!']);
        }

        return response(['errors' => 'Erro ao atualizar contato'], 500);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            return response(['errors' => 'Contato não encontrado'], 404);
        }

        try {
            $contact->delete();
            return response(['message' => 'Contato excluído com sucesso']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
