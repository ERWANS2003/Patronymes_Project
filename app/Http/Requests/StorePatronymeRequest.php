<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatronymeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canContribute();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Section I: Informations sur l'enquêté
            'date_collecte' => 'required|date',
            'collecteur' => 'required|string|max:255|min:2',
            'code_fiche' => 'required|string|max:50|min:1',
            'contact' => 'nullable|string|max:255',
            'enquete_nom' => 'required|string|max:255|min:2',
            'enquete_age' => 'nullable|integer|min:1|max:120',
            'enquete_sexe' => 'nullable|in:M,F',
            'enquete_fonction' => 'nullable|string|max:255',
            'enquete_telephone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'enquete_email' => 'nullable|email|max:255',

            // Section II: Informations sur le patronyme
            'nom' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/u',
                'unique:patronymes,nom'
            ],
            'groupe_ethnique_id' => 'nullable|exists:groupe_ethniques,id',
            'origine' => 'nullable|string|max:1000',
            'signification' => 'nullable|string|max:2000',
            'histoire' => 'nullable|string|max:5000',
            'langue_id' => 'nullable|exists:langues,id',
            'transmission' => 'nullable|in:pere,mere,autre',
            'patronyme_sexe' => 'nullable|in:M,F,mixte',
            'totem' => 'nullable|string|max:255',
            'justification_totem' => 'nullable|string|max:1000',
            'parents_plaisanterie' => 'nullable|string|max:1000',

            // Localisation
            'region_id' => 'nullable|exists:regions,id',
            'province_id' => 'nullable|exists:provinces,id',
            'commune_id' => 'nullable|exists:communes,id',

            // Champs additionnels
            'departement_id' => 'nullable|exists:departements,id',
            'frequence' => 'nullable|integer|min:0|max:100000',
            'ethnie_id' => 'nullable|exists:ethnies,id',
            'mode_transmission_id' => 'nullable|exists:mode_transmissions,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Section I: Messages pour l'enquêté
            'date_collecte.required' => 'La date de collecte est obligatoire.',
            'date_collecte.date' => 'La date de collecte doit être une date valide.',
            'collecteur.required' => 'Le nom du collecteur est obligatoire.',
            'collecteur.min' => 'Le nom du collecteur doit contenir au moins 2 caractères.',
            'code_fiche.required' => 'Le code fiche est obligatoire.',
            'code_fiche.min' => 'Le code fiche doit contenir au moins 1 caractère.',
            'code_fiche.max' => 'Le code fiche ne peut pas dépasser 50 caractères.',
            'enquete_nom.required' => 'Le nom de l\'enquêté est obligatoire.',
            'enquete_nom.min' => 'Le nom de l\'enquêté doit contenir au moins 2 caractères.',
            'enquete_age.min' => 'L\'âge doit être supérieur à 0.',
            'enquete_age.max' => 'L\'âge doit être inférieur à 120 ans.',
            'enquete_sexe.in' => 'Le sexe doit être M ou F.',
            'enquete_telephone.regex' => 'Le format du téléphone n\'est pas valide.',
            'enquete_email.email' => 'L\'adresse email doit être valide.',

            'nom.required' => 'Le nom du patronyme est obligatoire.',
            'nom.unique' => 'Ce patronyme existe déjà dans la base de données.',
            'nom.regex' => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',

            'groupe_ethnique_id.exists' => 'Le groupe ethnique sélectionné n\'existe pas.',
            'langue_id.exists' => 'La langue sélectionnée n\'existe pas.',
            'transmission.in' => 'La transmission doit être père, mère ou autre.',
            'patronyme_sexe.in' => 'Le sexe du patronyme doit être M, F ou mixte.',

            'region_id.exists' => 'La région sélectionnée n\'existe pas.',
            'province_id.exists' => 'La province sélectionnée n\'existe pas.',
            'commune_id.exists' => 'La commune sélectionnée n\'existe pas.',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas.',
            'ethnie_id.exists' => 'L\'ethnie sélectionnée n\'existe pas.',
            'mode_transmission_id.exists' => 'Le mode de transmission sélectionné n\'existe pas.',

            'frequence.min' => 'La fréquence ne peut pas être négative.',
            'frequence.max' => 'La fréquence ne peut pas dépasser 100 000.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'date_collecte' => 'date de collecte',
            'collecteur' => 'collecteur',
            'code_fiche' => 'code fiche',
            'contact' => 'contact',
            'enquete_nom' => 'nom de l\'enquêté',
            'enquete_age' => 'âge de l\'enquêté',
            'enquete_sexe' => 'sexe de l\'enquêté',
            'enquete_fonction' => 'fonction de l\'enquêté',
            'enquete_telephone' => 'téléphone de l\'enquêté',
            'enquete_email' => 'email de l\'enquêté',
            'groupe_ethnique_id' => 'groupe ethnique',
            'langue_id' => 'langue',
            'patronyme_sexe' => 'sexe du patronyme',
            'region_id' => 'région',
            'province_id' => 'province',
            'commune_id' => 'commune',
            'departement_id' => 'département',
            'ethnie_id' => 'ethnie',
            'mode_transmission_id' => 'mode de transmission',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validation croisée pour la localisation
            if ($this->province_id && $this->region_id) {
                $province = \App\Models\Province::find($this->province_id);
                if ($province && $province->region_id != $this->region_id) {
                    $validator->errors()->add('province_id', 'La province n\'appartient pas à la région sélectionnée.');
                }
            }

            if ($this->commune_id && $this->province_id) {
                $commune = \App\Models\Commune::find($this->commune_id);
                if ($commune && $commune->province_id != $this->province_id) {
                    $validator->errors()->add('commune_id', 'La commune n\'appartient pas à la province sélectionnée.');
                }
            }
        });
    }
}
