<?php namespace Onmeda\Controller;

use Onmeda\Controller\HomeAppController as AppController;
use RushCon\Core\Console;
use RushCon\Core\Register;
use Onmeda\Model\Config\SanteObject  as jsonFile;

class SanteController extends AppController {
    private $__drugsHistory;
    private $__drugsModified;
    private $__drugsDisabled;
    private $__drugsNew;
    private $__drugComponents;
    private $__drugsGenerics;
    private $__drugsPharmacieAddOn;
    private $__drugsProduitsIfp;
    private $__drugsProduitsAmmTextes;
    private $__drugsIdentProduits;
    private $__drugsCisAgents;
    private $__santeDrugRecord;
    private $__santeDrugIndicesRecord;
    
     public function __construct() {
        parent::__construct();        
        $this->__drugsHistory = Register::table("Onmeda.Historiques", $this->credentials);
        $this->__drugsNew = Register::table("Onmeda.HistoriqueProduitsNouveaux", $this->credentials);
        $this->__drugsModified = Register::table("Onmeda.HistoriqueProduitsModifies", $this->credentials);
        $this->__drugsDisabled = Register::table("Onmeda.HistoriqueProduitsSupprimes", $this->credentials);
        $this->__drugComponents = Register::table("Onmeda.CompoProduitsPa", $this->credentials);
        $this->__drugsGenerics = Register::table("Onmeda.IdentGeneriques", $this->credentials);
        $this->__drugsPharmacieAddOn = Register::table("Onmeda.PharmacieAddOn", $this->credentials);
        $this->__drugsProduitsIfp = Register::table("Onmeda.ProduitsIfp", $this->credentials);
        $this->__drugsProduitsAmmTextes = Register::table("Onmeda.ProduitsAmmTextes", $this->credentials);
        $this->__drugsIdentProduits = Register::table("Onmeda.IdentProduits", $this->credentials);
        $this->__drugsCisAgents = Register::table("Onmeda.CisCipAgence", $this->credentials);
        
        
        //$this->__mongoSourceClient = new Mongo();
    }
    
    public function indexAction () {
    }
    
    
    public function updateAction($dateInput = "") {
        //$drugId
      
        //$this->__santeDrugRecord = new \stdClass();
        $santeDrugRecord = new \stdClass();
        
        $santeDrugRecord->drugs  = array();
        $recordset = new \stdClass();
        
        if (empty($dateInput)) {
            $now = new \DateTime();
            $dt = $now->format("Y/m");
        } else {
            if ($this->__checkInputDateFormat($dateInput)) {
                $dt = $dateInput;
            }
        }
        //set data for table sante_drugs
        $recordset->id = "9" . $drugId;
        $recordset->CIP13 = "34009" . $drugId;
        $conditions = array("historique_produits_modifies.CODE_CIP" => $drugId, "historique_produits_modifies.DATE_" => "'" . trim($dt) . "'");
        //check if isHospitalier firts
        $drugs = $this->__drugsModified->find(array(
            'conditions' => $conditions
        ));
        $isHospitalier = $drugs[0]['HOSPITALIER'];
        $recordset->isHospitalier = $isHospitalier;
        
        //check ingredients
        $ingredients = $this->__getIngredients($drugId);
        $recordset->PrincipesActifs = $ingredients;
        /*END INGREDIENTS*/
        
        /*check if referent*/
        $isReferent = 0;
        $referentList = $this->__checkIfReferent($drugId);
        if (!empty($referentList)) {
            $isReferent = 1;
        }
        $recordset->isReferent = $isReferent;
        /*END*/
        
        /*check if generics*/
        $isGeneric = $this->__checkIfGeneric($drugId);
        $recordset->isGeneric = (int)$isGeneric;
        /*END*/
        
        /*Check if IsOrdonnance, we have to query table produits_ifp*/
        $isOrdonnance = 0;
        $ifp = $this->__getProduitsIfp($drugId);
        
        $tableCode = trim($ifp[0]['TABLEAU']);
        if (!empty($tableCode)) {
            $isOrdonnance = 1;
        }
        $recordset->isOrdonnance = $isOrdonnance;
        //END
        
        //get texts
        $texts = $this->__getTexts($drugId);
        $recordset->texts = $texts;
        
        $recordset->LastImport = 0;
        $recordset->NbNotes = 0;
        $recordset->SommeNotes = 0;
        $recordset->CommentaireDoc = 0;
        //END setting data for table sante_drugs
        
        //set data for table sante_drugs_indices
        $recordset->sante_drug_id =  "9" . $drugId;
        //check referent
        $recordset->ReferentId = 0;
        if ($recordset->isGeneric !== 0) {
            $recordset->ReferentId = $this->__getGenericRootId($drugId);
        }
        $recordset->TagFormePrincipale = 0;
        $recordset->TagSousRubrique = 0;
        $recordset->TagRubrique = 0;
        $recordset->TagColonne = 0;
        
        //General drug info: name, url ...
        $recordset->Medicament = $this->__cleanDrugName($ifp[0]['LIBELLELONG']);
        $identProduits = $this->__getIdentProduits($drugId);
        $recordset->MedicamentURL = $this->__setUrlName($identProduits[0]['LIBELLE_ABREGE']);
       
        $recordset->NomCommercial = $this->__setCommercialName($identProduits[0]['LIBELLE_ABREGE']);
        $recordset->isSupprime = 0;
        $recordset->Forme = $this->__getDrugForm($drugId);
        //ENd
        array_push($santeDrugRecord->drugs, $recordset);
        
        $this->__writeJsonObject($santeDrugRecord, "modified_drugs");
        
    }
    
    private function __setData($drugId) {
        
        $recordset = new \stdClass();
        $recordset->id = "9" . $drugId;
        $recordset->CIP13 = "34009" . $drugId;
        //check ingredients
        $ingredients = $this->__getIngredients($drugId);
        $recordset->PrincipesActifs = $ingredients;
        /*END INGREDIENTS*/
        
        /*check if referent*/
        $isReferent = 0;
        $referentList = $this->__checkIfReferent($drugId);
        if (!empty($referentList)) {
            $isReferent = 1;
        }
        $recordset->isReferent = $isReferent;
        /*END*/
        
        /*check if generics*/
        $isGeneric = $this->__checkIfGeneric($drugId);
        $recordset->isGeneric = (int)$isGeneric;
        /*END*/
        
        /*Check if IsOrdonnance, we have to query table produits_ifp*/
        $isOrdonnance = 0;
        $ifp = $this->__getProduitsIfp($drugId);
        
        $tableCode = trim($ifp[0]['TABLEAU']);
        if (!empty($tableCode)) {
            $isOrdonnance = 1;
        }
        $recordset->isOrdonnance = $isOrdonnance;
        //END
        
        //get texts
        $texts = $this->__getTexts($drugId);
        $recordset->texts = $texts;
        
        $recordset->LastImport = 0;
        $recordset->NbNotes = 0;
        $recordset->SommeNotes = 0;
        $recordset->CommentaireDoc = 0;
        //END setting data for table sante_drugs
        
        //set data for table sante_drugs_indices
        $recordset->sante_drug_id =  "9" . $drugId;
        //check referent
        $recordset->ReferentId = 0;
        if ($recordset->isGeneric !== 0) {
            $recordset->ReferentId = $this->__getGenericRootId($drugId);
        }
        $recordset->TagFormePrincipale = 0;
        $recordset->TagSousRubrique = 0;
        $recordset->TagRubrique = 0;
        $recordset->TagColonne = 0;
        
        //General drug info: name, url ...
        $recordset->Medicament = $this->__cleanDrugName($ifp[0]['LIBELLELONG']);
        $identProduits = $this->__getIdentProduits($drugId);
        $recordset->MedicamentURL = $this->__setUrlName($identProduits[0]['LIBELLE_ABREGE']);
       
        $recordset->NomCommercial = $this->__setCommercialName($identProduits[0]['LIBELLE_ABREGE']);
        $recordset->isSupprime = 0;
        $recordset->Forme = $this->__getDrugForm($drugId);
        //ENd
        return $recordset;
    }
    
    public function getDataAction($dateInput = "") {
        
        
         if (empty($dateInput)) {
            $now = new \DateTime();
            $dt = $now->format("Y/m");
        } else {
            if ($this->__checkInputDateFormat($dateInput)) {
                $dt = $dateInput;
            }
        }
        $allData = new \stdClass();
        $allData->modifiedDrugs = $this->__getModified($dt);
        $allData->newDrugs = $this->__getNew($dt);
        $allData->disabledDrugs = $this->__getDisabled($dt);
       // $this->__writeJsonObject($allData, "all_drugs");
    }
    
    private function __getModified($date) {
        
        $modifiedDrugs = $this->__drugsModified->find(array(
            'conditions' => array("historique_produits_modifies.DATE_" => "'" . trim($date) . "'")
        ));
        $allModified = array();
        foreach ($modifiedDrugs as $key => $value) {
            Console::pprintln("Modified drug: " . $value['CODE_CIP']);
            $tmpData = $this->__setData($value['CODE_CIP']);   
            $tmpData->isHospitalier = $value['HOSPITALIER'];
            $allModified[] = $tmpData;
        }
        
        return $allModified;
    }
    
    private function __getNew($date) {
        $newDrugs = $this->__drugsNew->find(array(
             'conditions' => array("historique_produits_nouveaux.DATE_" => "'" . trim($date) . "'")
        ));
        
        $allNewDrugs = array();
        foreach ($newDrugs as $key => $value) {
            Console::pprintln("New-Drug: " . $value['CODE_CIP']);
            $tmpData = $this->__setData($value['CODE_CIP']);
            $tmpData->PrixTTC = $value['PRIX_VENTE_TTC'];
            $tmpData->TauxSS = (double)$value['TAUX_SS'];
            $allNewDrugs[] = $tmpData;
        }
        return $allNewDrugs;
    }
    
    private function __getDisabled($date) {
        $disabledDrugs = $this->__drugsDisabled->find(array(
            "conditions" => array("historique_produits_supprimes.DATE_" => "'" . trim($date) . "'")
        ));
        
        $allDisabledDrugs = array();
        foreach ($disabledDrugs as $key => $value) {
             Console::pprintln("Disabled drug: " . $value['CODE_CIP']);
            $tmpData = $this->__setData($value['CODE_CIP']);
            $tmpData->DateSuppression = $this->__prepareDateTime($value['DATE_']);
            $tmpData->isSupprime = 1;
            $allDisabledDrugs[] = $tmpData;
        }
        
        return $allDisabledDrugs;
    }

    
    private function __checkInputDateFormat($input = "") {
        $pattern = "[0-9]{4}[\/](0|1)[0-9]";
        return preg_match("/$pattern/", $input);
    }
    
    private function __getIngredients($drugId) {
         $joins = array("type" => "LEFT JOIN","tables" => array(
                "compo_produits_pa" => array(
                    "compo_pa" => array("compo_produits_pa.CODE_PA" => "compo_pa.CODE_PA")
                ), 
            ));
        $fields = array("compo_produits_pa.CODE_CIP", "compo_produits_pa.CODE_PA","compo_pa.CODE_PA", "compo_pa.LIBELLE_PA");
        
        
        $conditions = array("compo_produits_pa.CODE_CIP" => $drugId);
        
        $additionals = array(
            "fields" => $fields,
            "join" => $joins,
            "conditions" => $conditions
            );
        $ingredients = $this->__drugComponents->find($additionals);
        return $ingredients;
    }
    
    private function __checkIfReferent($drugId) {
        $conditions  = array("ident_generiques.CODE_CIP_REFERENT" => $drugId);
        $subDrugs = $this->__drugsGenerics->find(array(
            'conditions' => $conditions
        ));
        
        return $subDrugs;
    }
    
    private function __checkIfGeneric($drugId) {
        $conditions = array("pharmacie_add_on.CODE_CIP" => $drugId);
        $fields = array("pharmacie_add_on.CODE_CIP", "pharmacie_add_on.GENERIQUE");
        $generic = $this->__drugsPharmacieAddOn->find(array(
            'fields' => $fields,
            'conditions' => $conditions
        ));
        return $generic[0]['GENERIQUE'];   
    }
    
    private function __getProduitsIfp($drugId) {
        $conditions = array("produits_ifp.CODE_CIP" => $drugId);
        $ifp = $this->__drugsProduitsIfp->find(array(
            'conditions' => $conditions
        ));
        return $ifp;
    }
    
    private function __getTexts($drugId) {
        $fields = array(
            "produits_amm_textes.CONTRE_INDICATION_RTF",
            "produits_amm_textes.PRECAUTION_EMPLOI_RTF", 
            "produits_amm_textes.EFFET_INDESIRABLE_RTF",
            "produits_amm_textes.GROSSESSE_ALLAITEMENT_RTF",
            "produits_ammplus_textes.PHARMACOCINETIQUE_RTF", 
            "produits_ammplus_textes.CONDUITE_RTF",
            "produits_ammplus_textes.INTERACTION_RTF");
        
        $joins = array("type" => "INNER JOIN","tables" => array(
                "produits_amm_textes" => array(
                    "produits_ammplus_textes" => array("produits_amm_textes.CODE_CIP" => "produits_ammplus_textes.CODE_CIP")
                ), 
            ));

        $conditions = array("produits_amm_textes.CODE_CIP" => $drugId, "produits_ammplus_textes.CODE_CIP" => $drugId);

        $texts = $this->__drugsProduitsAmmTextes->find(array(
            'join' => $joins,
            'fields' => $fields,
            'conditions' => $conditions
        ));
        
        //prepare texts first
        //echo utf8_decode(addslashes($texts[0]['CONDUITE_RTF']));
        $preparedTexts = array();
        $preparedTexts['ContreIndications'] = utf8_encode($texts[0]['CONTRE_INDICATION_RTF']);
        $preparedTexts['PrecautionsEmploi'] = utf8_encode($texts[0]['PRECAUTION_EMPLOI_RTF']);
        $preparedTexts['EffetsIndesirables'] = utf8_encode($texts[0]['EFFET_INDESIRABLE_RTF']);
        $preparedTexts['GrossesseAllaitement'] = utf8_encode($texts[0]['GROSSESSE_ALLAITEMENT_RTF']);
        $preparedTexts['Pharmacocinetique'] = utf8_encode($texts[0]['PHARMACOCINETIQUE_RTF']);
        $preparedTexts['ConduiteVehicules'] = utf8_encode($texts[0]['CONDUITE_RTF']);
        $preparedTexts['Interactions'] = $texts[0]['INTERACTION_RTF'];
        
        return $preparedTexts;
     }
     
     private function __writeJsonObject($content, $filename = "modified_drugs", $mode = 'a') {
         $json = new jsonFile();
         if (file_exists($json->santeFileObject['location'] . "/" . $filename . ".json")) {
             unlink($json->santeFileObject['location'] . "/" . $filename . ".json");
         }
        $filecontent = json_encode($content);
        $handler = fopen($json->santeFileObject['location'] . "/" . $filename . ".json", $mode);
        fwrite($handler, $filecontent);
        fclose($handler);
           
     }
     
     
     private function __getGenericRootId($drugId) {
         $conditions = array("ident_generiques.CODE_CIP_GENERIQUE" => $drugId);
         $fields = array("ident_generiques.CODE_CIP_REFERENT");
         $generics = $this->__drugsGenerics->find(array(
             "fields" => $fields,
            "conditions" => $conditions
         ));
         $referentId = 0;
         
        if (!empty($generics)) {
             $referentId =  $generics[0]['CODE_CIP_REFERENT'];
        }
         return $referentId;
     }
     
     private function __getIdentProduits($drugId) {
         $conditions = array("ident_produits.CODE_CIP" => $drugId);
         $identProduits = $this->__drugsIdentProduits->find(array(
             'conditions' => $conditions
         ));
         return $identProduits;
     }
     
    private function __cleanDrugName($drugName) {
        //trim and get rid of comma it exists
        $text = trim(str_replace(",", "",$drugName));
        return utf8_encode($text);
    }
    
    private function __setUrlName($drugName) {
        $urlName = preg_replace("/(\.| )/", "-", trim(strtolower($drugName)));
        return utf8_encode($urlName);
    }
    
    private function __setCommercialName($drugName) {
        $drugParts = explode(" ",strtolower($drugName));
        $map = array_map("ucfirst", $drugParts);
        return implode(" ", $map);
    }
    
    private function __getDrugForm($drugId) {
        $fields = array("cis_cip_agence.CODECIS", "cis_cip_agence.CODECIP", "cis_agence.FORME");
        $joins = array("type" => "INNER JOIN","tables" => array(
                "cis_cip_agence" => array(
                    "cis_agence" => array("cis_cip_agence.CODECIS" => "cis_agence.CODE")
                ), 
            ));
        $conditions = array("cis_cip_agence.CODECIP" => $drugId);
        $drugForm = $this->__drugsCisAgents->find(array(
            'fields' => $fields,
            'join' => $joins,
            "conditions" => $conditions
        ));
        $form = isset($drugForm[0]) ? utf8_encode($drugForm[0]["FORME"]) : "";
        return $form;
    }
    
    private function __prepareDateTime($dateStringFromDb) {
        $setDate = str_replace("/", "-", $dateStringFromDb);
        return $setDate . "-01 00:00:00";
    }
}

