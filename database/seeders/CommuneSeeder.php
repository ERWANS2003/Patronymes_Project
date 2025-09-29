<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commune;
use App\Models\Province;

class CommuneSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les provinces existantes
        $provinces = Province::all()->keyBy('nom');

        $communes = [
            // Kadiogo (Centre)
            'Kadiogo' => [
                'Ouagadougou', 'Komsilga', 'Tanghin-Dassouri', 'Saaba', 'Koubri', 'Loumbila'
            ],

            // Boulgou (Centre-Est)
            'Boulgou' => [
                'Bittou', 'Garango', 'Tenkodogo', 'Bané', 'Béguédo', 'Bissiga', 'Comin-Yanga', 'Ipelcé', 'Manga', 'Niaogho', 'Po', 'Sangue', 'Sapouy', 'Zabré', 'Zonsé'
            ],

            // Kouritenga (Centre-Est)
            'Kouritenga' => [
                'Andemtenga', 'Baskouré', 'Bissiga', 'Cinkansé', 'Dourtenga', 'Gounghin', 'Kando', 'Koupéla', 'Pouytenga', 'Sangue', 'Tensobentenga', 'Yargo'
            ],

            // Bam (Centre-Nord)
            'Bam' => [
                'Kongoussi', 'Bourzanga', 'Guibaré', 'Kongoussi', 'Nasséré', 'Rollo', 'Sabcé', 'Tikaré'
            ],

            // Namentenga (Centre-Nord)
            'Namentenga' => [
                'Boulsa', 'Bouroum', 'Dargo', 'Nagbingou', 'Pissila', 'Tougo', 'Yalgo'
            ],

            // Sanmatenga (Centre-Nord)
            'Sanmatenga' => [
                'Barsalogho', 'Boussouma', 'Kaya', 'Korsimoro', 'Manga', 'Namissiguima', 'Pensa', 'Pibaoré', 'Pissila', 'Rollo', 'Tougo'
            ],

            // Boulkiemdé (Centre-Ouest)
            'Boulkiemdé' => [
                'Boulkiemdé', 'Imasgo', 'Kindi', 'Kokologo', 'Nandiala', 'Pella', 'Poa', 'Ramongo', 'Sabou', 'Sourgou', 'Thyou'
            ],

            // Sissili (Centre-Ouest)
            'Sissili' => [
                'Bouroum-Bouroum', 'Léo', 'Nahouri', 'Nobéré', 'Sapouy', 'Séguénéga', 'Silly', 'Toécé'
            ],

            // Ziro (Centre-Ouest)
            'Ziro' => [
                'Cassou', 'Dano', 'Diébougou', 'Gao', 'Iolonioro', 'Nako', 'Sapouy', 'Séguénéga'
            ],

            // Bazèga (Centre-Sud)
            'Bazèga' => [
                'Gaongo', 'Ipelcé', 'Kayao', 'Kombissiri', 'Saponé', 'Toécé'
            ],

            // Nahouri (Centre-Sud)
            'Nahouri' => [
                'Guiaro', 'Pô', 'Tiébélé', 'Zabré'
            ],

            // Zoundwéogo (Centre-Sud)
            'Zoundwéogo' => [
                'Béguédo', 'Bissiga', 'Gomboussougou', 'Guiba', 'Manga', 'Nobéré', 'Sapouy', 'Zabré'
            ],

            // Gnagna (Est)
            'Gnagna' => [
                'Bilanga', 'Bogandé', 'Coalla', 'Liptougou', 'Manni', 'Piéla', 'Thion'
            ],

            // Gourma (Est)
            'Gourma' => [
                'Diabo', 'Fada N\'Gourma', 'Matiacoali', 'Pama', 'Tibga'
            ],

            // Komondjari (Est)
            'Komondjari' => [
                'Bartiébougou', 'Gayéri', 'Komondjari', 'Manni'
            ],

            // Kompienga (Est)
            'Kompienga' => [
                'Kompienga', 'Madjoari', 'Pama'
            ],

            // Tapoa (Est)
            'Tapoa' => [
                'Diapaga', 'Kantchari', 'Logobou', 'Namounou', 'Partiaga', 'Tambaga'
            ],

            // Houet (Hauts-Bassins)
            'Houet' => [
                'Bama', 'Bobo-Dioulasso', 'Dandé', 'Fara', 'Koundougou', 'Léna', 'Péni', 'Satiri', 'Toussiana'
            ],

            // Kénédougou (Hauts-Bassins)
            'Kénédougou' => [
                'Banfora', 'Mangodara', 'Niangoloko', 'Orodara', 'Sindou', 'Soubakaniédougou'
            ],

            // Tuy (Hauts-Bassins)
            'Tuy' => [
                'Béréba', 'Founzan', 'Houndé', 'Karangasso-Vigué', 'Koumbia', 'Léna', 'Sindou'
            ],

            // Loroum (Nord)
            'Loroum' => [
                'Banh', 'Ouahigouya', 'Sollé'
            ],

            // Passoré (Nord)
            'Passoré' => [
                'Arbinda', 'Barsalogho', 'Boussouma', 'Kaya', 'Pibaoré', 'Rollo', 'Tougo'
            ],

            // Yatenga (Nord)
            'Yatenga' => [
                'Kombissiri', 'Ouahigouya', 'Séguénéga', 'Thyou'
            ],

            // Zondoma (Nord)
            'Zondoma' => [
                'Gourcy', 'Kombissiri', 'Ouahigouya', 'Thyou'
            ],

            // Ganzourgou (Plateau-Central)
            'Ganzourgou' => [
                'Boudry', 'Méguet', 'Zorgho'
            ],

            // Kourwéogo (Plateau-Central)
            'Kourwéogo' => [
                'Boussé', 'Laye', 'Pabré', 'Ziniaré'
            ],

            // Oubritenga (Plateau-Central)
            'Oubritenga' => [
                'Absouya', 'Dapélogo', 'Loumbila', 'Ziniaré'
            ],

            // Oudalan (Sahel)
            'Oudalan' => [
                'Déou', 'Gorom-Gorom', 'Markoye', 'Oursi'
            ],

            // Séno (Sahel)
            'Séno' => [
                'Bani', 'Dori', 'Falagountou', 'Gorgadji', 'Sampelga'
            ],

            // Soum (Sahel)
            'Soum' => [
                'Arbinda', 'Djibo', 'Koutougou', 'Pobé-Mengao', 'Tongomayel'
            ],

            // Yagha (Sahel)
            'Yagha' => [
                'Mansila', 'Sebba', 'Solhan', 'Tankougounadié'
            ],

            // Bougouriba (Sud-Ouest)
            'Bougouriba' => [
                'Diébougou', 'Iolonioro', 'Nako', 'Sapouy'
            ],

            // Ioba (Sud-Ouest)
            'Ioba' => [
                'Dano', 'Diébougou', 'Nako', 'Sapouy'
            ],

            // Noumbiel (Sud-Ouest)
            'Noumbiel' => [
                'Batié', 'Diébougou', 'Nako'
            ],

            // Poni (Sud-Ouest)
            'Poni' => [
                'Gaoua', 'Kampti', 'Loropéni', 'Nako'
            ],

            // Comoé (Cascades)
            'Comoé' => [
                'Banfora', 'Mangodara', 'Niangoloko', 'Soubakaniédougou'
            ],

            // Léraba (Cascades)
            'Léraba' => [
                'Douna', 'Kankalaba', 'Loumana', 'Sindou'
            ],

            // Balé (Boucle du Mouhoun)
            'Balé' => [
                'Bagassi', 'Bana', 'Boromo', 'Fara', 'Pompoï', 'Sapouy'
            ],

            // Banwa (Boucle du Mouhoun)
            'Banwa' => [
                'Balavé', 'Kouka', 'Sami', 'Solenzo'
            ],

            // Kossi (Boucle du Mouhoun)
            'Kossi' => [
                'Barani', 'Bomborokuy', 'Djibasso', 'Doumbala', 'Kombori', 'Madouba', 'Nouna'
            ],

            // Mouhoun (Boucle du Mouhoun)
            'Mouhoun' => [
                'Bondokuy', 'Dédougou', 'Kona', 'Ouarkoye', 'Safané', 'Tcheriba'
            ],

            // Nayala (Boucle du Mouhoun)
            'Nayala' => [
                'Gassan', 'Gossina', 'Toma', 'Yaba'
            ],

            // Sourou (Boucle du Mouhoun)
            'Sourou' => [
                'Di', 'Gassan', 'Kassoum', 'Lanfiéra', 'Lankoué', 'Tougan'
            ],
        ];

        foreach ($communes as $provinceName => $communeList) {
            $province = $provinces->get($provinceName);
            if (!$province) {
                continue;
            }

            foreach ($communeList as $communeName) {
                Commune::firstOrCreate(
                    ['nom' => $communeName, 'province_id' => $province->id],
                    [
                        'nom' => $communeName,
                        'province_id' => $province->id,
                    ]
                );
            }
        }
    }
}
