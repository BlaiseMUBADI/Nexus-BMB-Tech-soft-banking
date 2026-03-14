<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $accounts = [
            // Classe 1
            ['numero' => '1', 'libelle' => 'Comptes de capitaux', 'parent' => null, 'mov' => false],
            ['numero' => '10', 'libelle' => 'Capital', 'parent' => '1', 'mov' => false],
            ['numero' => '101', 'libelle' => 'Capital social', 'parent' => '10', 'mov' => false],
            ['numero' => '1011', 'libelle' => 'Capital souscrit appele verse', 'parent' => '101', 'mov' => true],
            ['numero' => '102', 'libelle' => 'Capital souscrit non appele', 'parent' => '10', 'mov' => true],
            ['numero' => '103', 'libelle' => 'Capital souscrit appele non verse', 'parent' => '10', 'mov' => true],
            ['numero' => '104', 'libelle' => 'Primes liees au capital', 'parent' => '10', 'mov' => true],
            ['numero' => '105', 'libelle' => 'Ecarts de reevaluation', 'parent' => '10', 'mov' => true],
            ['numero' => '106', 'libelle' => 'Reserves', 'parent' => '10', 'mov' => true],
            ['numero' => '107', 'libelle' => 'Report a nouveau', 'parent' => '10', 'mov' => true],
            ['numero' => '108', 'libelle' => 'Resultat net en instance d affectation', 'parent' => '10', 'mov' => true],
            ['numero' => '109', 'libelle' => 'Actionnaires capital souscrit non appele', 'parent' => '10', 'mov' => true],
            ['numero' => '11', 'libelle' => 'Emprunts et dettes assimilees', 'parent' => '1', 'mov' => false],
            ['numero' => '12', 'libelle' => 'Dettes de location acquisition', 'parent' => '1', 'mov' => false],
            ['numero' => '13', 'libelle' => 'Provisions pour risques et charges', 'parent' => '1', 'mov' => false],
            ['numero' => '14', 'libelle' => 'Dettes financieres diverses', 'parent' => '1', 'mov' => false],
            ['numero' => '15', 'libelle' => 'Dettes rattachees a des participations', 'parent' => '1', 'mov' => false],
            ['numero' => '16', 'libelle' => 'Fonds affectes et subventions d investissement', 'parent' => '1', 'mov' => false],
            ['numero' => '17', 'libelle' => 'Autres fonds propres', 'parent' => '1', 'mov' => false],
            ['numero' => '18', 'libelle' => 'Comptes de liaison des etablissements', 'parent' => '1', 'mov' => false],
            ['numero' => '19', 'libelle' => 'Provisions financieres pour risques et charges', 'parent' => '1', 'mov' => false],

            // Classe 2
            ['numero' => '2', 'libelle' => 'Comptes d immobilisations', 'parent' => null, 'mov' => false],
            ['numero' => '20', 'libelle' => 'Charges immobilisees', 'parent' => '2', 'mov' => false],
            ['numero' => '21', 'libelle' => 'Immobilisations incorporelles', 'parent' => '2', 'mov' => false],
            ['numero' => '22', 'libelle' => 'Terrains', 'parent' => '2', 'mov' => false],
            ['numero' => '23', 'libelle' => 'Batiments installations techniques et agencements', 'parent' => '2', 'mov' => false],
            ['numero' => '24', 'libelle' => 'Materiel mobilier et actifs biologiques', 'parent' => '2', 'mov' => false],
            ['numero' => '25', 'libelle' => 'Avances et acomptes verses sur immobilisations', 'parent' => '2', 'mov' => false],
            ['numero' => '251', 'libelle' => 'Avances et acomptes sur immobilisations corporelles', 'parent' => '25', 'mov' => false],
            ['numero' => '2511', 'libelle' => 'Depots a vue clients', 'parent' => '251', 'mov' => true, 'type' => 'PASSIF'],
            ['numero' => '2512', 'libelle' => 'Depots a terme clients', 'parent' => '251', 'mov' => true, 'type' => 'PASSIF'],
            ['numero' => '26', 'libelle' => 'Titres de participation et autres immobilisations financieres', 'parent' => '2', 'mov' => false],
            ['numero' => '27', 'libelle' => 'Ecarts de conversion actif', 'parent' => '2', 'mov' => false],
            ['numero' => '28', 'libelle' => 'Amortissements', 'parent' => '2', 'mov' => false],
            ['numero' => '29', 'libelle' => 'Depreciations des immobilisations', 'parent' => '2', 'mov' => false],

            // Classe 3
            ['numero' => '3', 'libelle' => 'Comptes de stocks', 'parent' => null, 'mov' => false],
            ['numero' => '31', 'libelle' => 'Marchandises', 'parent' => '3', 'mov' => false],
            ['numero' => '32', 'libelle' => 'Matieres premieres et fournitures liees', 'parent' => '3', 'mov' => false],
            ['numero' => '33', 'libelle' => 'Autres approvisionnements', 'parent' => '3', 'mov' => false],
            ['numero' => '34', 'libelle' => 'Produits en cours', 'parent' => '3', 'mov' => false],
            ['numero' => '35', 'libelle' => 'Services en cours', 'parent' => '3', 'mov' => false],
            ['numero' => '36', 'libelle' => 'Produits finis', 'parent' => '3', 'mov' => false],
            ['numero' => '37', 'libelle' => 'Produits intermediaires et residuels', 'parent' => '3', 'mov' => false],
            ['numero' => '38', 'libelle' => 'Stocks en cours de route et en consignation', 'parent' => '3', 'mov' => false],
            ['numero' => '39', 'libelle' => 'Depreciations des stocks', 'parent' => '3', 'mov' => false],

            // Classe 4
            ['numero' => '4', 'libelle' => 'Comptes de tiers', 'parent' => null, 'mov' => false],
            ['numero' => '40', 'libelle' => 'Fournisseurs et comptes rattaches', 'parent' => '4', 'mov' => false],
            ['numero' => '41', 'libelle' => 'Clients et comptes rattaches', 'parent' => '4', 'mov' => false],
            ['numero' => '411', 'libelle' => 'Clients ordinaires', 'parent' => '41', 'mov' => false],
            ['numero' => '4111', 'libelle' => 'Comptes courants clients', 'parent' => '411', 'mov' => true, 'type' => 'PASSIF'],
            ['numero' => '4112', 'libelle' => 'Comptes epargne clients', 'parent' => '411', 'mov' => true, 'type' => 'PASSIF'],
            ['numero' => '412', 'libelle' => 'Clients effets a recevoir', 'parent' => '41', 'mov' => true],
            ['numero' => '42', 'libelle' => 'Personnel', 'parent' => '4', 'mov' => false],
            ['numero' => '43', 'libelle' => 'Organismes sociaux', 'parent' => '4', 'mov' => false],
            ['numero' => '44', 'libelle' => 'Etat et collectivites publiques', 'parent' => '4', 'mov' => false],
            ['numero' => '45', 'libelle' => 'Organismes internationaux', 'parent' => '4', 'mov' => false],
            ['numero' => '46', 'libelle' => 'Associes et groupe', 'parent' => '4', 'mov' => false],
            ['numero' => '47', 'libelle' => 'Debiteurs et crediteurs divers', 'parent' => '4', 'mov' => false],
            ['numero' => '471', 'libelle' => 'Comptes d attente', 'parent' => '47', 'mov' => false],
            ['numero' => '4711', 'libelle' => 'Compte transitoire operations de change', 'parent' => '471', 'mov' => true, 'type' => 'PASSIF'],
            ['numero' => '48', 'libelle' => 'Comptes de regularisation', 'parent' => '4', 'mov' => false],
            ['numero' => '49', 'libelle' => 'Depreciations et provisions des comptes de tiers', 'parent' => '4', 'mov' => false],

            // Classe 5
            ['numero' => '5', 'libelle' => 'Comptes de tresorerie', 'parent' => null, 'mov' => false],
            ['numero' => '50', 'libelle' => 'Titres de placement', 'parent' => '5', 'mov' => false],
            ['numero' => '51', 'libelle' => 'Valeurs a encaisser', 'parent' => '5', 'mov' => false],
            ['numero' => '52', 'libelle' => 'Banques etablissements financiers et assimiles', 'parent' => '5', 'mov' => false],
            ['numero' => '521', 'libelle' => 'Banques locales', 'parent' => '52', 'mov' => false],
            ['numero' => '5211', 'libelle' => 'Banque locale CDF', 'parent' => '521', 'mov' => true],
            ['numero' => '5212', 'libelle' => 'Banque locale USD', 'parent' => '521', 'mov' => true],
            ['numero' => '53', 'libelle' => 'Etablissements financiers et instruments monetaires', 'parent' => '5', 'mov' => false],
            ['numero' => '54', 'libelle' => 'Instruments de tresorerie', 'parent' => '5', 'mov' => false],
            ['numero' => '55', 'libelle' => 'Monnaie electronique', 'parent' => '5', 'mov' => false],
            ['numero' => '56', 'libelle' => 'Banques crediteurs', 'parent' => '5', 'mov' => false],
            ['numero' => '57', 'libelle' => 'Caisse', 'parent' => '5', 'mov' => false],
            ['numero' => '570', 'libelle' => 'Caisse principale', 'parent' => '57', 'mov' => false],
            ['numero' => '5701', 'libelle' => 'Caisse CDF', 'parent' => '570', 'mov' => true],
            ['numero' => '5702', 'libelle' => 'Caisse USD', 'parent' => '570', 'mov' => true],
            ['numero' => '5703', 'libelle' => 'Caisse EUR', 'parent' => '570', 'mov' => true],
            ['numero' => '58', 'libelle' => 'Virements internes', 'parent' => '5', 'mov' => false],
            ['numero' => '581', 'libelle' => 'Virements internes en cours', 'parent' => '58', 'mov' => false],
            ['numero' => '5811', 'libelle' => 'Virements internes en cours CDF', 'parent' => '581', 'mov' => true],
            ['numero' => '59', 'libelle' => 'Depreciations des comptes financiers', 'parent' => '5', 'mov' => false],

            // Classe 6
            ['numero' => '6', 'libelle' => 'Comptes de charges des activites ordinaires', 'parent' => null, 'mov' => false],
            ['numero' => '60', 'libelle' => 'Achats et variation de stocks', 'parent' => '6', 'mov' => false],
            ['numero' => '600', 'libelle' => 'Achats', 'parent' => '60', 'mov' => false],
            ['numero' => '6001', 'libelle' => 'Frais bancaires', 'parent' => '600', 'mov' => true],
            ['numero' => '61', 'libelle' => 'Transports', 'parent' => '6', 'mov' => false],
            ['numero' => '62', 'libelle' => 'Services exterieurs A', 'parent' => '6', 'mov' => false],
            ['numero' => '63', 'libelle' => 'Services exterieurs B', 'parent' => '6', 'mov' => false],
            ['numero' => '64', 'libelle' => 'Impots et taxes', 'parent' => '6', 'mov' => false],
            ['numero' => '65', 'libelle' => 'Autres charges', 'parent' => '6', 'mov' => false],
            ['numero' => '66', 'libelle' => 'Charges de personnel', 'parent' => '6', 'mov' => false],
            ['numero' => '67', 'libelle' => 'Frais financiers et charges assimilees', 'parent' => '6', 'mov' => false],
            ['numero' => '68', 'libelle' => 'Dotations aux amortissements provisions et depreciations', 'parent' => '6', 'mov' => false],
            ['numero' => '69', 'libelle' => 'Impots sur resultats', 'parent' => '6', 'mov' => false],

            // Classe 7
            ['numero' => '7', 'libelle' => 'Comptes de produits des activites ordinaires', 'parent' => null, 'mov' => false],
            ['numero' => '70', 'libelle' => 'Ventes', 'parent' => '7', 'mov' => false],
            ['numero' => '700', 'libelle' => 'Produits financiers courants', 'parent' => '70', 'mov' => false],
            ['numero' => '7001', 'libelle' => 'Interets et produits assimiles', 'parent' => '700', 'mov' => true],
            ['numero' => '701', 'libelle' => 'Ventes de produits finis', 'parent' => '70', 'mov' => false],
            ['numero' => '702', 'libelle' => 'Ventes de produits intermediaires', 'parent' => '70', 'mov' => false],
            ['numero' => '703', 'libelle' => 'Ventes de produits residuels', 'parent' => '70', 'mov' => false],
            ['numero' => '704', 'libelle' => 'Travaux factures', 'parent' => '70', 'mov' => false],
            ['numero' => '705', 'libelle' => 'Etudes facturees', 'parent' => '70', 'mov' => false],
            ['numero' => '706', 'libelle' => 'Services vendus', 'parent' => '70', 'mov' => false],
            ['numero' => '7061', 'libelle' => 'Commissions sur services bancaires', 'parent' => '706', 'mov' => true],
            ['numero' => '707', 'libelle' => 'Produits accessoires', 'parent' => '70', 'mov' => false],
            ['numero' => '7071', 'libelle' => 'Produits services guichet', 'parent' => '707', 'mov' => true],
            ['numero' => '708', 'libelle' => 'Produits divers', 'parent' => '70', 'mov' => false],
            ['numero' => '71', 'libelle' => 'Subventions d exploitation', 'parent' => '7', 'mov' => false],
            ['numero' => '72', 'libelle' => 'Production immobilisee', 'parent' => '7', 'mov' => false],
            ['numero' => '73', 'libelle' => 'Variations des stocks de biens et services produits', 'parent' => '7', 'mov' => false],
            ['numero' => '74', 'libelle' => 'Produits divers', 'parent' => '7', 'mov' => false],
            ['numero' => '75', 'libelle' => 'Transferts de charges', 'parent' => '7', 'mov' => false],
            ['numero' => '76', 'libelle' => 'Revenus financiers et produits assimiles', 'parent' => '7', 'mov' => false],
            ['numero' => '77', 'libelle' => 'Produits exceptionnels', 'parent' => '7', 'mov' => false],
            ['numero' => '78', 'libelle' => 'Reprises de provisions et amortissements', 'parent' => '7', 'mov' => false],
            ['numero' => '79', 'libelle' => 'Transferts de produits', 'parent' => '7', 'mov' => false],

            // Classe 8
            ['numero' => '8', 'libelle' => 'Comptes des autres charges et autres produits', 'parent' => null, 'mov' => false],
            ['numero' => '81', 'libelle' => 'Valeurs comptables des cessions d immobilisations', 'parent' => '8', 'mov' => false],
            ['numero' => '82', 'libelle' => 'Produits des cessions d immobilisations', 'parent' => '8', 'mov' => false],
            ['numero' => '83', 'libelle' => 'Charges hors activites ordinaires', 'parent' => '8', 'mov' => false],
            ['numero' => '84', 'libelle' => 'Produits hors activites ordinaires', 'parent' => '8', 'mov' => false],
            ['numero' => '85', 'libelle' => 'Dotations hors activites ordinaires', 'parent' => '8', 'mov' => false],
            ['numero' => '86', 'libelle' => 'Reprises hors activites ordinaires', 'parent' => '8', 'mov' => false],
            ['numero' => '87', 'libelle' => 'Participations des travailleurs', 'parent' => '8', 'mov' => false],
            ['numero' => '88', 'libelle' => 'Subventions d equilibre', 'parent' => '8', 'mov' => false],
            ['numero' => '89', 'libelle' => 'Bilan ouverture et cloture', 'parent' => '8', 'mov' => false],
        ];

        foreach ($accounts as $account) {
            $numero = (string) $account['numero'];
            $classe = substr($numero, 0, 1);

            DB::table('tb_plan_comptable')->updateOrInsert(
                ['numero_compte' => $numero],
                [
                    'libelle' => $account['libelle'],
                    'classe_ohada' => $classe,
                    'parent_compte' => $account['parent'] ?? null,
                    'niveau' => strlen($numero),
                    'type_compte' => $account['type'] ?? $this->resolveTypeFromClasse($classe),
                    'est_mouvementable' => (bool) ($account['mov'] ?? true),
                    'est_actif' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        $numeroComptes = [
            '1','10','101','1011','102','103','104','105','106','107','108','109','11','12','13','14','15','16','17','18','19',
            '2','20','21','22','23','24','25','251','2511','2512','26','27','28','29',
            '3','31','32','33','34','35','36','37','38','39',
            '4','40','41','411','4111','4112','412','42','43','44','45','46','47','471','4711','48','49',
            '5','50','51','52','521','5211','5212','53','54','55','56','57','570','5701','5702','5703','58','581','5811','59',
            '6','60','600','6001','61','62','63','64','65','66','67','68','69',
            '7','70','700','7001','701','702','703','704','705','706','7061','707','7071','708','71','72','73','74','75','76','77','78','79',
            '8','81','82','83','84','85','86','87','88','89',
        ];

        DB::table('tb_plan_comptable')->whereIn('numero_compte', $numeroComptes)->delete();
    }

    private function resolveTypeFromClasse(string $classe): string
    {
        return match ($classe) {
            '1' => 'PASSIF',
            '2' => 'ACTIF',
            '3' => 'ACTIF',
            '4' => 'MIXTE',
            '5' => 'ACTIF',
            '6' => 'CHARGE',
            '7' => 'PRODUIT',
            '8' => 'HORS_BILAN',
            default => 'MIXTE',
        };
    }
};
