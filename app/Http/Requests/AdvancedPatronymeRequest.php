<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdvancedPatronymeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nom' => ['required', 'string', 'max:255', 'unique:patronymes,nom,' . $this->route('patronyme')],
            'signification' => ['required', 'string', 'min:10'],
            'origine' => ['required', 'string', 'max:255'],
            'histoire' => ['nullable', 'string'],
            'region_id' => ['required', 'exists:regions,id'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'commune_id' => ['nullable', 'exists:communes,id'],
            'groupe_ethnique_id' => ['required', 'exists:groupe_ethniques,id'],
            'langue_id' => ['required', 'exists:langues,id'],
            'frequence' => ['required', 'integer', 'min:1', 'max:10000'],
            'enquete_nom' => ['required', 'string', 'max:255'],
            'enquete_age' => ['required', 'integer', 'min:1', 'max:120'],
            'enquete_sexe' => ['required', Rule::in(['M', 'F'])],
            'enquete_fonction' => ['nullable', 'string', 'max:255'],
            'enquete_contact' => ['nullable', 'string', 'max:255'],
            'transmission' => ['required', Rule::in(['pere', 'mere', 'autre'])],
            'patronyme_sexe' => ['required', Rule::in(['M', 'F', 'mixte'])],
            'totem' => ['nullable', 'string', 'max:255'],
            'justification_totem' => ['nullable', 'string'],
            'parents_plaisanterie' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'nom.required' => 'Le nom du patronyme est obligatoire.',
            'nom.unique' => 'Ce patronyme existe déjà dans la base de données.',
            'signification.required' => 'La signification est obligatoire.',
            'signification.min' => 'La signification doit contenir au moins 10 caractères.',
            'origine.required' => 'L\'origine est obligatoire.',
            'region_id.required' => 'La région est obligatoire.',
            'region_id.exists' => 'La région sélectionnée n\'existe pas.',
            'groupe_ethnique_id.required' => 'Le groupe ethnique est obligatoire.',
            'groupe_ethnique_id.exists' => 'Le groupe ethnique sélectionné n\'existe pas.',
            'langue_id.required' => 'La langue est obligatoire.',
            'langue_id.exists' => 'La langue sélectionnée n\'existe pas.',
            'frequence.required' => 'La fréquence est obligatoire.',
            'frequence.integer' => 'La fréquence doit être un nombre entier.',
            'frequence.min' => 'La fréquence doit être d\'au moins 1.',
            'frequence.max' => 'La fréquence ne peut pas dépasser 10000.',
            'enquete_nom.required' => 'Le nom de l\'enquêté est obligatoire.',
            'enquete_age.required' => 'L\'âge de l\'enquêté est obligatoire.',
            'enquete_age.integer' => 'L\'âge doit être un nombre entier.',
            'enquete_age.min' => 'L\'âge doit être d\'au moins 1 an.',
            'enquete_age.max' => 'L\'âge ne peut pas dépasser 120 ans.',
            'enquete_sexe.required' => 'Le sexe de l\'enquêté est obligatoire.',
            'enquete_sexe.in' => 'Le sexe doit être Masculin ou Féminin.',
            'transmission.required' => 'Le mode de transmission est obligatoire.',
            'transmission.in' => 'Le mode de transmission doit être père, mère ou autre.',
            'patronyme_sexe.required' => 'Le sexe du patronyme est obligatoire.',
            'patronyme_sexe.in' => 'Le sexe du patronyme doit être Masculin, Féminin ou Mixte.',
        ];
    }
}
