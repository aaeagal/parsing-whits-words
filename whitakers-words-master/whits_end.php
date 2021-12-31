<?php

//This is an API endpoint from the original ADA software
function whit (string $word)
{
    chdir('/var/www/parsingwhitswords/whitakers-words-master');
    $ret = null;
    exec('bin/words ' . $word, $ret);
    return $ret;
}

//This is used to help with formating the line counter
//From: https://stackoverflow.com/questions/3109978/display-numbers-with-ordinal-suffix-in-php 
// @lacopo, thanks! 
function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
    {
       return $number. 'th';
    }
    else
    {
        return $number. $ends[$number % 10];
    }
        
}


function translate($dict_entry)
{   
    // this array holds the definition
    $definition = array();
    // for pres verb
    $pres_bool = NULL;
    // for perf verb
    $perf_bool = NULL;
    // for pl. nouns
    $plu_bool = NULL;
    // for impf verbs
    $impf_bool = NULL;
    //for fut perf
    $futp_bool = NULL;
    //plup bool 
    $plup_bool = NULL;
    
    
    for($i=0; $i < sizeof($dict_entry); $i++)
    {   

        //getting the definitions and adding them to the definintion array
        if(strpos($dict_entry[$i], '[X'))
        {
            $i++;
            $multi_def = explode(",", $dict_entry[$i]);
            $def_ele = (string) $multi_def[0];
            array_push($definition, $def_ele);     
        }
//-------------------------------------------------noun---------------------------------------------------------------------------
        if(strpos($dict_entry[$i], "  N  ") && strpos($dict_entry[$i], " P") && strpos($dict_entry[$i], "NOM"))
        {
            $plu_bool = TRUE;
        }
        
//------------------------------------------------to be verbs---------------------------------------------------------------------
        // Present, Active
        if(strpos($dict_entry[$i], "PRES") && strpos($dict_entry[$i], "ACTIVE"))
        {   
            $pres_bool = TRUE;
            
        }
        // Imperfect, Active, Plural 
        if(strpos($dict_entry[$i], "IMPF") && strpos($dict_entry[$i], "ACTIVE") && strpos($dict_entry[$i], " P"))
        {   
            $verb_to_be = array("were");
            array_splice($definition, 2, 0,$verb_to_be);
            $impf_bool = TRUE;
            
        }

        // Imperfect, Active, Singluar to be 
        if(strpos($dict_entry[$i], "IMPF") && strpos($dict_entry[$i], "ACTIVE") && strpos($dict_entry[$i], " S"))
        {   
            $verb_to_be = array("was");
            array_splice($definition, 2, 0,$verb_to_be);
            $impf_bool = TRUE;
        }

        // Perfect, Active
        if(strpos($dict_entry[$i], "PERF") && strpos($dict_entry[$i], "ACTIVE"))
        {   
            $perf_bool = TRUE;
        }


        // Future, Active 
        if(strpos($dict_entry[$i], "FUT") && strpos($dict_entry[$i], "ACTIVE") &! strpos($dict_entry[$i], "FUTP"))
        {   
            $verb_to_be = array("will");
            array_splice($definition, 2, 0,$verb_to_be); 
        }

        // Future, Perfect, Active
        if(strpos($dict_entry[$i], "FUTP"))
        {   
            $verb_to_be = array("will have");
            array_splice($definition, 2, 0,$verb_to_be); 
            $futp_bool = TRUE;
        }

        //Pluperfect, Active
        if(strpos($dict_entry[$i], "PLUP") && strpos($dict_entry[$i], "ACTIVE"))
        {   
            $verb_to_be = array("had");
            array_splice($definition, 2, 0,$verb_to_be); 
            $plup_bool = TRUE;
        }
        //-----------------------------------------Passive----------------------------------------------------------
        //Present Passive
        if(strpos($dict_entry[$i], "PRES") && strpos($dict_entry[$i], "PASSIVE"))
        {   
            $verb_to_be = array("is");
            array_splice($definition, 2, 0,$verb_to_be); 
        }
        //Future Passive
        if(strpos($dict_entry[$i], "FUT") && strpos($dict_entry[$i], "PASSIVE") &! strpos($dict_entry[$i], "FUTP"))
        {   
            $verb_to_be = array("will be");
            array_splice($definition, 2, 0,$verb_to_be); 
        }
        //Imperfect Passive
        if(strpos($dict_entry[$i], "IMPF") && strpos($dict_entry[$i], "PASSIVE"))
        {   
            $verb_to_be = array("was being");
            array_splice($definition, 2, 0,$verb_to_be); 
        }
        //Perfect Passive
        if(strpos($dict_entry[$i], "PERF") && strpos($dict_entry[$i], "PASSIVE"))
        {   
            $verb_to_be = array("was");
            array_splice($definition, 2, 0,$verb_to_be); 
        }
        //Pluperfect Passive
        if(strpos($dict_entry[$i], "PLUP") && strpos($dict_entry[$i], "PASSIVE"))
        {   
            $verb_to_be = array("had been");
            array_splice($definition, 2, 0,$verb_to_be); 
        }
        //Future Perfect Passive
        if(strpos($dict_entry[$i], "FUTP") && strpos($dict_entry[$i], "PASSIVE"))
        {   
            $verb_to_be = array("will have been");
            array_splice($definition, 2, 0,$verb_to_be); 
        }

    }

//------------------------------------------verb add ons------------------------------------------------------    
    if($plu_bool === TRUE || $pres_bool === TRUE)
    {
        $definition[0] = $definition[0] . 's';
    }

    if($impf_bool === TRUE)
    {
        $definition[1] = $definition[1] . 'ing';
    
    }

    if($perf_bool === TRUE)
    {
        $definition[0] = $definition[0] . 'ed';
    }

    if($futp_bool === TRUE || $plup_bool === TRUE)
    {
        $definition[1] = $definition[1] . 'ed';
    }
// print definition
    foreach ($definition as $values)
    {
        echo " $values ";
    }
}

function parse ($dict_entry)
{
    // for line number
    $line_num = 1;
    //for noun boolean
    $noun_bool = NULL;
    //for verb boolean
    $verb_bool = NULL;
    

    for($i=0; $i < sizeof($dict_entry)-3; $i++)
    {
        //shares with the user the line of the dictionary entry
        
        echo "\n\n";
        echo ordinal($line_num) . " line:\n";
        echo "$dict_entry[$i]\n";

        //PARTS OF SPEECH: NOUN, PRONOUN, PRONOUN W PREP
        //ADJECTIVE, ADVERB, VERB, PREPOSITION, CONJUNCTION
        //INTERJECTION
        
        //information on noun
        if(strpos($dict_entry[$i], '   N    '))
        {
            echo "\nN = NOUN: This word is a person, place, or thing.\n";
            $noun_bool = true;

        }
        //information on pronoun
        if(strpos($dict_entry[$i], '  PRON   '))
        {
            echo "\nPRON = PRONOUN: This word used in place of a specific noun mentioned earlier in a sentence so that you don’t have to keep saying/writing that particular noun.\n";
            $noun_bool = true; 
        }
        //information on pronoun with preposition
        if(strpos($dict_entry[$i], '  PACK   '))
        {
            echo "\nPACK = PRONOUN WITH PREPOSITION: This word used in place of a specific noun mentioned earlier in a sentence so that you don’t have to keep saying/writing that particular noun with a prepostion.\n";
            $noun_bool = true;
        }
        //information on adjective
         if(strpos($dict_entry[$i], '  ADJ  '))
         {
             echo "\nADJ = ADJECTIVE: This word used to describe the corrosponding noun.\n";
             $noun_bool = true;
         }
        //information on adverb
        if(strpos($dict_entry[$i], '  ADV  '))
        {
            echo "\nADV = ADVERB: This word used to describe the corrosponding verb.\n";
            $noun_bool = true;
        }
        //information on verb
        if(strpos($dict_entry[$i], '  V  '))
        {
            echo "\nV = VERB: This word is an action, state or occurance.\n";
            $verb_bool = true;
        }
        //information on prepositon 
        if(strpos($dict_entry[$i], '  PREP  '))
        {
            echo "\nPREP = PREPOSITION: This word describes a relationship between two nouns.\n";
            $noun_bool = true;
        }
        //information on conjunction
        if(strpos($dict_entry[$i], '  CONJ  '))
        {
            echo "\nCONJ = CONJUNCTION: This word connects two words together.\n";
            $noun_bool = true;
        }
        //information on interjection
        if(strpos($dict_entry[$i], '  INTERJ  '))
        {
            echo "\nINTERJ = INTERJECTION: This word shows emotion.\n";
            $noun_bool = true;
        }
        //information on number
        if(strpos($dict_entry[$i], '  NUM  '))
        {
            echo "\nNUM = NUMBER: This word shows quantity.\n";
            $noun_bool = true;
        }  

        //DECLENSION: 1st, 2nd, 3rd, 4th, 5th

        if($noun_bool === true)
        {
        preg_match_all('!\d+!', $dict_entry[$i], $matches);
  
        //information on first declension
        if($matches[0][0] == "1")
        {
            echo "\n1 = 1st DECLENSION: This word is in the first declension.\n";
        }
        //information on second declension
        if($matches[0][0] == '2')
        {
            echo "\n2 = 2nd DECLENSION: This word is in the second declension.\n";
        }
        //information on third declension
        if($matches[0][0] == '3')
        {
            echo "\n3 = 3rd DECLENSION: This word is in the third declension.\n";
        }
        //information on fourth declension
        if($matches[0][0] == '4')
        {
            echo "\n4 = 4th DECLENSION: This word is in the third declension.\n";
        }
        //information on fifth declension
        if($matches[0][0] == '5')
        {
            echo "\n5 = 5th DECLENSION: This word is in the fifth declension.\n";
        }

        }
        
        //CASES: NOM, ACC, DATIVE, ABLATIVE, VOCATIVE

        //information on the nominative case
        if(strpos($dict_entry[$i], 'NOM'))
        {
            echo "\nNOM = NOMINATIVE CASE: This noun could be the subject of the sentence or the complement of the verb.\n";
        }
        //information on the accusative case
        if(strpos($dict_entry[$i], 'ACC'))
        {
            echo "\nACC = ACCUSATIVE CASE: This noun could be the direct object of the sentence. This case is also used for the direct object of verbs, after certain prepositions, for expressing duration of time or motion towards something and adverbially.\n";
        }
        //information on the dative case
        if(strpos($dict_entry[$i], 'DAT'))
        {
            echo "\nDAT = DATIVE CASE: This noun could be the indirect object (to or for), but it has a very wide range of meanings beyond this.\n";
        }
        //information on the genitive case
        if(strpos($dict_entry[$i], 'GEN'))
        {
            echo "\nGEN = GENITIVE CASE: This noun could be denoting possession (of) but it has a very wide range of meanings beyond this and it contains the stem of the noun. \n";
        }
        //information on the ablative case
        if(strpos($dict_entry[$i], 'ABL'))
        {
            echo "\nABL = ABLATIVE CASE: This noun could be expressing means, association or separation (by, with or from), but it has a very wide range of meanings beyond these. It is also used after certain prepositions.\n";
        }
        //information on the vocative case
        if(strpos($dict_entry[$i], 'VOC'))
        {
            echo "\nVOC = VOCATIVE CASE: This noun could be used when someone is addressing someone else directly. It is almost always the same as the nominative.\n";
        }

        //NUMBER: SINGULAR, PLURAL

        //information on singular
        if(strpos($dict_entry[$i], 'S'))
        {
            echo "\nS = SINGULAR: This noun is refering to one thing.\n";
        }
        //information on plural
        if(strpos($dict_entry[$i], 'P'))
        {
            echo "\nP = PLURAL: This noun is referring to multiple things.\n";
        }


        //GENDER: MASCULINE, FEMININE, NEUTER, COMMON

        //information on masculine
        if(strpos($dict_entry[$i], ' M '))
        {
            echo "\nM = MASCULINE: The gender of this word is masculine.\n";
        }
        //information on feminine
        if(strpos($dict_entry[$i], ' F'))
        {
            echo "\nF = FEMININE: The gender of this word is feminine.\n";
        }
        //information on neuter (different)
        $frag_dict = explode(' ', $dict_entry[$i]);
        $check_neuter = array_pop($frag_dict);

        if($check_neuter == 'N')
        {
            echo "\nN = NEUTER: The gender of this word is Neuter \n";
        }
        //information on common
        if(strpos($dict_entry[$i], ' C '))
        {
            echo "\nC = COMMON: The gender of this word is common -- either masculine or feminine.\n";
        }
        
        //-------------------------------VERBS------------------------------------------------------------

        //used read numbers
        preg_match_all('!\d+!', $dict_entry[$i], $matches);
        
        
        
        //CONJUNGATION: 1st, 2nd, and 3rd 

        //information on first conjugation
        if($matches[0][0] == "1" && $verb_bool === true)
        {
            echo "\n1 = 1st CONJUGATATION: This verb is a 1st conjugation verb. A conjugation is a group of verbs which share similarities in appearance (not in meaning or usage). The first conjugation verbs end in -o -āre, like porto portāre (carry).\n";
        }
        //information on 2nd conjugation
        if($matches[0][0] == "2" && $verb_bool === true)
        {
            echo "\n2 = 2nd CONJUGATATION: This verb is a 2nd conjugation verb. A conjugation is a group of verbs which share similarities in appearance (not in meaning or usage). The second conjugation verbs end in -ēo -ēre, like habēo habēre (have).\n";
        }
        //information on 3rd conjugation
        if($matches[0][0] == "3" && $verb_bool === true)
        {
            echo "\n3 = 3rd CONJUGATATION: This verb is a 3rd conjugation verb. A conjugation is a group of verbs which share similarities in appearance (not in meaning or usage). The third conjugation verbs end in -o -ere, like rego regere (rule).\n";
        }
        //information on 4th conjugation 
        if($matches[0][0] == "4" && $verb_bool === true)
        {
            echo "\n4 = 4th CONJUGATATION: This verb is a 4th conjugation verb. A conjugation is a group of verbs which share similarities in appearance (not in meaning or usage). The fourth conjugation verbs end in -io -ı¯re, like audio audı¯re (hear).\n";
        }

        //TENSE: PRESENT, IMPERFECT, FUTURE, PERFECT, FUTURE PERFECT 

        //information on present
        if(strpos($dict_entry[$i], ' PRES '))
        {
            echo "\nPRES = PRESENT TENSE: The present tense refers to actions which occur in the present. We can express this in different ways in English, e.g. I am going to school, I go to school, or even, I do go to school.\n";
        }
        //information on imperfect
        if(strpos($dict_entry[$i], ' IMPF '))
        {
            echo "\nIMPF = IMPERFECT TENSE: The imperfect tense is used for actions in the past which did not get finished, went on regularly, lasted for some time before they ended or only just got started.\n";
        }
        //information on perfect
        if(strpos($dict_entry[$i], ' PERF '))
        {
            echo "\nPERF = PERFECT TENSE: The perfect tense refers mostly to single, completed actions in the past. In English we use either the past tense on its own or together with the verbs have or did, e.g. I carried, I have carried or I did carry.\n";
        }
        //information on pluperfect
        if(strpos($dict_entry[$i], ' PLUP '))
        {
            echo "\nPLUP = PLUPERFECT TENSE:  The pluperfect tense is expressed in English by using had before the past participle of the verb to indicate an action which occurred at two stages back in the past, e.g. When the horse had bolted, I shut the stable door.\n";
        }
        //information on future
        if(strpos($dict_entry[$i], ' FUT '))
        {
            echo "\nFUT = FUTURE TENSE:  The future tense refers to actions that will happen in the future. In English we usually use the words will or shall before the verb, e.g. I will go, I shall go.\n";
        }
        //information on future perfect
        if(strpos($dict_entry[$i], ' FUTP '))
        {
            echo "\nFUTP = FUTURE PERFECT TENSE:  The future perfect tense refers to an action which will have taken place by a certain time in the future. In English, as the name of the tense suggests, we use the auxiliary verbs will (the future element) and have (the perfect element), e.g. They will have decided by the end of the day.\n";
        }

        //VOICE: ACTIVE & PASSIVE

        //information on active
        if(strpos($dict_entry[$i], ' ACTIVE '))
        {
            echo "\nACTIVE = ACTIVE VOICE:  The active voice is used when the subject of the sentence or clause is performing the action of the verb, e.g. The elephant chases the mouse.\n";
        }
        //information on passive
        if(strpos($dict_entry[$i], ' PASSIVE '))
        {
            echo "\nPASSIVE = PASSIVE VOICE:  he passive voice is used when the subject is experiencing the action of the verb, e.g. The elephant is chased by the mouse.\n";
        }

        //MOOD: INDICITIVE AND SUBJUNCTIVE
        //information on indicitive
        if(strpos($dict_entry[$i], ' IND '))
        {
            echo "\nIND = INDICITIVE: This verb is in the indicitive mood. The indicative mood is generally used for making statements and asking questions (e.g. Grass grows. Where is he going?). The main verb of a Latin sentence will usually be in the indicative mood.\n";
        }
        //information on subjuctive 
        if(strpos($dict_entry[$i], ' SUB '))
        {
            echo "\nSUB = SUBJUNCTIVE: This verb is in the subjunctive mood. The subjunctive mood is used mainly as a verb in subordinate clauses, often to express anticipated or conditional actions. On the less common occasions when it is a main verb, it usually expresses a wish and is often found in mottos \n";
        }
        
        //PERSON: 1st, 2nd, or 3rd

        //information on first person
        if($matches[0][2] == "1" && $verb_bool === true)
        {
            echo "\1 = 1st PERSON: In each tense six persons can perform the action. In first person singular, \"I\" performed the action, and first person plural \"We\" performed the action.\n";
        }
        //information on 2nd person
        if($matches[0][2] == "2" && $verb_bool === true)
        {
            echo "\2 = 2nd PERSON: In each tense six persons can perform the action. In second person singular, \"You\" performed the action, and second person plural \"You all\" performed the action.\n";
        }
        //information on 3rd person
        if($matches[0][2] == "3" && $verb_bool === true)
        {
            echo "\n3 = 3rd PERSON: In each tense six persons can perform the action. In third person singular, \"He, she, or it\" performed the action, and third person plural \"They\" performed the action.\n";
        }
        
        
        //-------------------------------BOOLEANS---------------------------------------------------------
        $line_num++;
        $verb_bool = NULL;
        $noun_bool = NULL;
    }



}
?>
