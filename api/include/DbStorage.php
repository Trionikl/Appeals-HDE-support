<?php
    
    /**
        * Class sqlfunc - работа с базой данных
    */
    class DbStorage {
        
        public $dbconnect;
        public $sqlstrquery = '';
        
        function __construct()
        {
            $dbcon = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_BASE);
            $this->dbconnect = $dbcon;
        }
        
        function getTickets($dialogID, $portal, $arStatus)
        {            
            foreach ($arStatus as $key => $value) {
                $strStatus .= "`STATUS`=" . "'". $value . "'" . " OR ";
            }
            $strStatus = "(" . rtrim($strStatus, " OR ") . ")";              
            $sql = "SELECT * FROM `ALL_TICKET` WHERE `DIALOGID`='".$dialogID."' AND `PORTAL`='".$portal."'" . " AND " . $strStatus;  
            
            $this->sqlstrquery = $sql;
            $result = $this->dbconnect->query($this->sqlstrquery);
            $arResult = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $arResult[] = $row;
            }
            
            return $arResult;
        }
        
        //Записать ID бота битрикс24 с привязкой к порталу, если портал существует - то обновить запись.
        function setIdBot($portal, $idBot) { 
            //проверить существует ли запись
            $sql = "SELECT * FROM `BOT_PORTAL` WHERE `URLPORTAL`='". $portal . "'";            
            $this->sqlstrquery = $sql;
            $result = $this->dbconnect->query($this->sqlstrquery);        
            if ($result) { //если запись существует - обновить портал, иначе создать запись с порталом
                $sql = "UPDATE `BOT_PORTAL` SET `BOT_ID`='$idBot' WHERE URLPORTAL='$portal'"; 
            }
            else {
                $sql = "INSERT INTO `BOT_PORTAL` (URLPORTAL, BOT_ID) VALUES ('$portal', '$idBot')"; 
            } 
            $this->sqlstrquery = $sql;
            $result = $this->dbconnect->query($this->sqlstrquery);         
            $arResult= $result; 
            return $arResult;
        }
        
        //прочитать ID бота битрикс24
        function getIdBot($portal) {        
            $sql = "SELECT * FROM `BOT_PORTAL` WHERE `URLPORTAL`='". $portal . "'";
            $this->sqlstrquery = $sql;
            $result = $this->dbconnect->query($this->sqlstrquery);
            $arResult = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $arResult[] = $row;
            }
            return $arResult;
        } 
        
        //удалить все записи в таблице BOT_PORTAL - ДЛЯ СЛУЖЕБНОГО ПОЛЬЗОВАНИЯ
        function deleteBotPortal() {        
            $sql = "DELETE FROM `BOT_PORTAL`";            
            $this->sqlstrquery = $sql;
            $result = $this->dbconnect->query($this->sqlstrquery);         
            $arResult= $result;    
            return $arResult;
        }

        //получить портал клиента из таблицы WEBHOOKS
        function getWebhook($portal) {
            $sql = "SELECT * FROM `WEBHOOKS` WHERE `PORTAL`='". $portal . "'";
            $this->sqlstrquery = $sql;
            $result = $this->dbconnect->query($this->sqlstrquery);
            $arResult = array();
            while($row = $result->fetch_array(MYSQLI_ASSOC))
            {
                $arResult = $row;
            }
            return $arResult;
        }

    }