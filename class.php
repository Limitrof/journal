<?php
/////////////////////////////////////// определение синглтона -  у класса есть только один экземпляр, и предоставляет к нему глобальную точку доступа ( в нашем случае это Singleton::getInstance()->имяПоляИлиФункции[()]
// базовый класс основной информации // P.S.: не статический класс, а Singleton для контроля за тем чтобы в приложении создавался всего одно соединение с базой данных, ресурс cоединения и сохраняется в объекте-одиночке
 class Singleton {
    protected static $instance;  // object instance экземпляр класса хранится в приватной статической переменной $instance
    private function __construct(){ /* ... @return Singleton */ }  // Защищаем от создания через new Singleton
    private function __clone()    { /* ... @return Singleton */ }  // Защищаем от создания через клонирование
    private function __wakeup()   { /* ... @return Singleton */ }  // Защищаем от создания через unserialize
    public static function getInstance() {    // Возвращает единственный экземпляр класса. @return Singleton
			if ( is_null(self::$instance) ) {
					////////////////////////////////////////////////////////////////
					// единожды за сеанс инициализируем соединение с базой данных //
				//	echo 'START TO DB!<hr>';
					define("DBName","UNI");
					define("HostName","localhost");
					define("UserName","----");
					define("Password","9060013----"); 
					if(!mysql_connect(HostName,UserName,Password)) 
					{  
					echo "error DB ".DBName."!<br>"; 
					echo mysql_error();
					exit; 
					}
					mysql_select_db(DBName);
					mysql_query("SET character_set_client='utf8'");
					mysql_query("SET character_set_connection='utf8'");
					mysql_query("SET character_set_results='utf8'");
					mysql_query("SET NAMES 'utf8'");
					// запустили //
					///////////////
            self::$instance = new self();
        }
        return self::$instance;
	}
	public $innerDBAuthor=false;
	public $projectPath = 'http://klio.org.ua/';// путь к основному веб-русурсу СИНТАКСИС ДЛЯ ОБРАЩЕНИЯ К ПОЛЮ - Singleton::getInstance()->projectPath;
	public $projectName = '_history_';// имя веб-проекта
	public $histansPath = 'http://histans.com/';// путь к ресурсу хранения контента
	public $innerPath = '/usr/www/histans.com/';// аюслютный путь к файлам на сервера (при upload[e])
	//////////////////////////////////
	// имена восьми основных таблиц //
	public $tabArticle="nl_bib_journal";//таблица статей
	public $tabBib="nl_bib";//таблица библиографии
	// имена восьми основных таблиц //
	//////////////////////////////////
	public function getMainQueryData(){
			return "isVis=1  AND (WebProject='".$this->projectName."' OR WebProject LIKE '%,".$this->projectName.",%' OR WebProject LIKE '".$this->projectName.",%' OR WebProject LIKE '%,".$this->projectName."') "; // СИНТАКСИС ДЛЯ ОБРАЩЕНИЯ К МЕТОДУ - Singleton::getInstance()->getMainQueryData();
	}//при выводе для пользователей обязательно контролировать "видимость" записи и принадлежность проекту
 }
// базовый класс основной информации //
///////////////////////////////////////
//////////////////////////////////////////////////////////
// класс-наследник с переопределенным полем projectPath //
class projectAndDBdata extends Singleton {
    protected static $instance;  // object instance экземпляр класса хранится в приватной статической переменной $instance
    private function __construct(){ /* ... @return Singleton */ }  // Защищаем от создания через new Singleton
    private function __clone()    { /* ... @return Singleton */ }  // Защищаем от создания через клонирование
    private function __wakeup()   { /* ... @return Singleton */ }  // Защищаем от создания через unserialize
    public static function getInstance() {    // Возвращает единственный экземпляр класса. @return Singleton
			if ( is_null(self::$instance) ) {
					////////////////////////////////////////////////////////////////
					// единожды за сеанс инициализируем соединение с базой данных //
					define("DBName","UNI");//ИМЯ АЛЬТЕРНАТИВНОЙ БАЗЫ ДАННЫХ
					define("HostName","localhost");//ИМЯ АЛЬТЕРНАТИВНОГО ХОСТА или ip
					define("UserName","----");//АЛЬТЕРНАТИВНЫЙ ЛОГИН
					define("Password","9060013----");//АЛЬТЕРНАТИВНЫЙ ПАРОЛЬ
					if(!mysql_connect(HostName,UserName,Password)) 
					{  
					echo "error DB ".DBName."!<br>"; 
					echo mysql_error();
					exit; 
					}
					mysql_select_db(DBName);
					mysql_query("SET character_set_client='utf8'");
					mysql_query("SET character_set_connection='utf8'");
					mysql_query("SET character_set_results='utf8'");
					mysql_query("SET NAMES 'utf8'");
					// запустили //
					///////////////
            self::$instance = new self();
        }
			return self::$instance;
	}
	public $innerDBAuthor=true;//управление поиском в базе данных по фамилии: если true, то поиск осуществляется только в границах внутренней базы ресурса
	public $projectName = '_Orient_';//ИМЯ АЛЬТЕРНАТИВНОГО ПРОЕКТА _zbroeznav_,_Orient_,_nsku_,_world_,_histans_
	public function getMainQueryData(){if(isset($this->projectName))	return " isVis=1  AND (WebProject='".$this->projectName."' OR WebProject LIKE '%,".$this->projectName.",%' OR WebProject LIKE '".$this->projectName.",%' OR WebProject LIKE '%,".$this->projectName."') ";
else return Singleton::getInstance()->getMainQueryData();
	}
}
// класс-наследник с переопределенным полем projectPath //
//////////////////////////////////////////////////////////
//
function avtorDrow($strInAvtor)
{

$spaceSplited=explode(',',$strInAvtor);
//return count($spaceSplited);
					if(count($spaceSplited)==1)
					{	
						$avtorOnParts=explode(' ',$strInAvtor);
						$avtorSurname=$avtorOnParts[0];

						//$nameAndPartom=explode(' ',$avtorOnParts[0]);
						//$avtorName=$nameAndPartom[0];
						//return '.'.$avtorSurname.'.';
						$getIt=projectAndDBdata::getInstance()->innerDBAuthor;
						if($getIt) $try=projectAndDBdata::getInstance()->getMainQueryData().' AND';else $try='';
						$query_LiberForSotFam = "SELECT id,image,Text FROM uni_str_sot WHERE ".$try." Surname='$avtorSurname'";
						$result_LiberForSotFam = mysql_query($query_LiberForSotFam) or die("Query failed");
						//$sot_key=substr($OnlyFam,0,strlen($OnlyFam)-4);
						$moreThenZero=0;$strOut='';
						while($onebyoneAvtor=mysql_fetch_array($result_LiberForSotFam))
						{ 
							$DirOfImage = 'http://histans.com/';
							if($moreThenZero==0) {$strOut="В базі даних істориків за запитом '".$avtorSurname."' знайдено:<br/>";
							$moreThenZero++;}
							 	

							if($onebyoneAvtor['id']!=''){
								$cuttedText=substr($onebyoneAvtor['Text'],0,200);
								$strOut.='<a href="http://oriental-studies.org.ua/?hist='.$onebyoneAvtor['id'].'">'.$strInAvtor.'
								<br/>		<img class="list_img" src="'.$DirOfImage.$onebyoneAvtor['image'].'
								" title="'.$onebyoneAvtor['id'].'"/><br/>'.$cuttedText.'...</a><hr/>';
						}
						}
						if($strOut!='') return $strOut; else return $strInAvtor;
											
					}
					else
					{
					$current=0;
					$outStrFams='';
					while($current!=count($spaceSplited))
						{
						$onlyOneAvtor=trim($spaceSplited[$current]);
						$avtorOnParts=explode(' ',$onlyOneAvtor);
						$avtorSurname=$avtorOnParts[0];
						//if(substr_count($onlyOneAvtor, ".")==2) $onlyOneAvtor=substr($onlyOneAvtor,0,strlen($OnlyFam)-4);
						//if(substr_count($onlyOneAvtor, ".")==1) $onlyOneAvtor=substr($onlyOneAvtor,0,strlen($OnlyFam)-2);
						$query_LiberForSotFam = "SELECT id FROM uni_str_sot WHERE Surname='$avtorSurname'";
						//$query_LiberForSotFam = "SELECT id FROM uni_str_sot WHERE Fam_short LIKE '$onlyOneAvtor%'";
						$result_LiberForSotFam = mysql_query($query_LiberForSotFam) or die("Query failed");
							$onebyoneAvtor=mysql_fetch_array($result_LiberForSotFam);
								if($onebyoneAvtor['id']!='')
								{
									if($current!=0) $outStrFams=$outStrFams.', <a href="?hist='.$onebyoneAvtor['id'].'">'.$spaceSplited[$current].'</a>';
									else $outStrFams='<a href="?hist='.$onebyoneAvtor['id'].'">'.$spaceSplited[$current].'</a>';
								}
								else
								{
								//echo 'not find for -'.$onlyOneAvtor.'- ';
									if($current!=0) $outStrFams=$outStrFams.', '.$spaceSplited[$current];
									else $outStrFams=$onlyOneAvtor;
								}
						mysql_free_result($result_LiberForSotFam);
						$current++;
						}
						return $outStrFams;
					} 
}
/////////
function avtorDrowEng($strInAvtor)
{

$spaceSplited=explode(',',$strInAvtor);
//return count($spaceSplited);
					if(count($spaceSplited)==1)
					{	
						$avtorOnParts=explode(' ',$strInAvtor);
						$avtorSurname=$avtorOnParts[1];

						//$nameAndPartom=explode(' ',$avtorOnParts[0]);
						//$avtorName=$nameAndPartom[0];
						//return '.'.$avtorSurname.'.';

						$query_LiberForSotFam = "SELECT id,image,Text FROM uni_str_sot WHERE FamEng='$avtorSurname'";
						$result_LiberForSotFam = mysql_query($query_LiberForSotFam) or die("Query failed");
						//$sot_key=substr($OnlyFam,0,strlen($OnlyFam)-4);
						$moreThenZero=0;$strOut='';$find=false;
						while($onebyoneAvtor=mysql_fetch_array($result_LiberForSotFam))
						{ 
							$DirOfImage = 'http://histans.com/';
							if($moreThenZero==0) {$strOut="В базі даних істориків за запитом '".$avtorSurname."' знайдено:<br/>";
							$moreThenZero++;}
							 	

							if($onebyoneAvtor['id']!=''){
								$find=true;
								$cuttedText=substr($onebyoneAvtor['Text'],0,200);
								$strOut.='<a href="http://oriental-studies.org.ua/?hist='.$onebyoneAvtor['id'].'">'.$strInAvtor.'
								<br/>		<img class="list_img" src="'.$DirOfImage.$onebyoneAvtor['image'].'
								" title="'.$onebyoneAvtor['id'].'"/><br/>'.$cuttedText.'...</a><hr/>';
							}
							
						}
						if($find) return $strOut;
											
					}
					else
					{
					$current=0;
					$outStrFams='';
					while($current!=count($spaceSplited))
						{
						$onlyOneAvtor=trim($spaceSplited[$current]);
						$avtorOnParts=explode(' ',$onlyOneAvtor);
						$avtorSurname=$avtorOnParts[0];
						//if(substr_count($onlyOneAvtor, ".")==2) $onlyOneAvtor=substr($onlyOneAvtor,0,strlen($OnlyFam)-4);
						//if(substr_count($onlyOneAvtor, ".")==1) $onlyOneAvtor=substr($onlyOneAvtor,0,strlen($OnlyFam)-2);
						$query_LiberForSotFam = "SELECT id FROM uni_str_sot WHERE Surname='$avtorSurname'";
						//$query_LiberForSotFam = "SELECT id FROM uni_str_sot WHERE Fam_short LIKE '$onlyOneAvtor%'";
						$result_LiberForSotFam = mysql_query($query_LiberForSotFam) or die("Query failed");
							$onebyoneAvtor=mysql_fetch_array($result_LiberForSotFam);
								if($onebyoneAvtor['id']!='')
								{
									if($current!=0) $outStrFams=$outStrFams.', <a href="?hist='.$onebyoneAvtor['id'].'">'.$spaceSplited[$current].'</a>';
									else $outStrFams='<a href="?hist='.$onebyoneAvtor['id'].'">'.$spaceSplited[$current].'</a>';
								}
								else
								{
								//echo 'not find for -'.$onlyOneAvtor.'- ';
									if($current!=0) $outStrFams=$outStrFams.', '.$spaceSplited[$current];
									else $outStrFams=$onlyOneAvtor;
								}
						mysql_free_result($result_LiberForSotFam);
						$current++;
						}
						return $outStrFams;
					} 
}

//
//
//////////////////////////////////////////////////
// функции для упрощения работы с базами данных //
function mysql_select($what,$from,$where='1') 
{
		$sql='select '.$what.' from `'.$from.'` where '.projectAndDBdata::getInstance()->getMainQueryData().$where.'';//оптимизирует запросы в соответствии с принадлежностью ресурса тому или иному проекту
	//	echo '<hr>'.$sql.'<hr>';
	$rez=mysql_query($sql) or die(mysql_error());
	return $rez;
}
function mysql_insert($into,$fields,$values) //mysql_insert("table","id,num","NULL,'1'"); -> last_id or ERROR
{
	$sql='INSERT INTO `'.$into.'` ('.$fields.') VALUES ('.$values.')';
	$rez=mysql_query($sql) or die(mysql_error());
	return mysql_insert_id();
}
function mysql_update($table,$values,$where='1') //mysql_update("table","id='9',num='8'","id=1"); -> TRUE or ERROR
{
	$sql='UPDATE `'.$table.'` SET '.$values.' WHERE '.$where.'';
	$rez=mysql_query($sql) or die(mysql_error());
	return $rez;
}
function mysql_delete($from,$where='1') //mysql_delete("table","id=1") -> TRUE or ERROR
{
	$sql='DELETE FROM `'.$from.'` WHERE '.$where.' ';
	$rez=mysql_query($sql) or die(mysql_error());
	return $rez;
}	
// функции для упрощения работы с базами данных //
//////////////////////////////////////////////////
//
//////////////////////////////////////////////////////
// функция удаляющая знаки пунктуации и спецсимволы //
function escComa($st)
{
	$st = str_replace(
		array(",",". ","#","№","-","(",")",":"), 	//что заменяем
		array( "", " ", "", "", "", "", "",""),		//на что заменяем
		$st
	);
	return $st;  
}

//////////////////////////
// storageUnitClass 001 //
class storageUnit
{
	/////////////////////////////
	// блок генерируемых полей //
	public $linkToUnit;//линк на веб-страницу с полными данными
	public $pathToImg;//путь к презентационной картинке
	public $navField;//содержимое навигационной строки (дополнительные данные и линки на них) для текущей единицы хранения (разные для каждого типа)
	// блок генерируемых полей //
	/////////////////////////////
	public $unic;//уникальный идентификатор записи - не путать с id
	//////////////////////////////////////
	// блок основных интерфейсных полей // 
	public $typeData;
	public $mainData;
	public $otherData;
	// блок основных интерфейсных полей // 
	//////////////////////////////////////
	//
	///////////////////////////////////////////////
	// блок обязательных полей для любого класса //
	//имена переменных полностью соответствуют названием столбцов таблицы в базе данных
	//=I=позиционная информация (всегда размещаются в начале таблицы)
protected $id;//-1-уникальное автоинкриментное поле; ВНИМАНИЕ: не привязывать данное значение к жестким ссылкам - использовать уникальные идентификаторы (isbn - книги,latinka - энциклопедия,unic - сайты)
public $webProject;//-2-принадлежность к сайту; первый в списке - идентификатор ресурса модераторами которого данная единица хранения была создана (соответствуют записям таблицы uni_webprojects: _zbroeznav_,_history_,_histans_,_Orient_,_nsku_,_world_ ...)
public $orderBy;//-3-позиция при вывод тематической подборки на основе выборки InProject разлчных единиц хранения
public $inProject;//-4-принадлежность к проекту - верхний уровень иерархии по отношению к полю Theme_deep; указывает принадлежность к тематическим проектам в соответствии с записями таблицы uni_projects (в некоторых случаях значения могут соответствовать полям theme_number таблицы uni_project_deep - при построении тематических иерархий содержащих три и более уровня)
public $themeDeep;//-5-подраздел проекта - нижний уровень иерархии по отношению к полю InProject; указывает принадлежность к подразделам соответствующих проектов из таблиц uni_projects и uni_project_deep
public $isVis;//-6-доступен ли просмотр записи для пользователей (ВНИМАНИЕ: управление данным полем должно быть доступно только для модераторов представляющих ресурс, на котором данная запись была создана - остальные ресурсы могут управлять видимостью запись через опицию "принадлежность к ресурсу" - uni_webprojects
public $thisOrigin;//-7-принадлежность ресурсу (является ли ресурс автором данного материала)
public $isTop;//-8-выводится ли информация в анонсы
	//=II=данные модерации (как правило размещаются сразу после позиционной информации)
public $firstLog;//-9-пользователь создавший запись
public $firstDate;//-10-дата и время создания записи
public $editLog;//-11-пользователь внесший последние изменения
public $editDate;//-12-дата и время последнего изменения
public $editNum;//-13-количество обновлений контента
	//=III=рейтинговые данные (размещаются в самом конце, в таблицах статей и книг - сразу за полем hitpdf)
public $hit;//-14-количество просмотров веб-страницы (ВНИМАНИЕ: еще одним  рейтинговым полем [не универсальным] является hitpdf количество загрузок - выводится для статей и книг имеющих полные версии в формате pdf)
public $hitpdf;//-ВНЕ НУМЕРАЦИИ- актуально для классов статей и библиографии
public $votes;//-15-количество голосов
public $votessum;//-16-общая сумма голосов
public $comments;//-17-комментарии, альтернатива плагинам социальных сетей (ВНИМАНИЕ: планируется исключить в новой версии)
//передаваемая в sql-запрос строка с перечислением универсальных полей - 16
//id(1),webProject(2),orderBy(3),inProject(4),themeDeep(5),isVis(6),thisOrigin(7),isTop(8), - позиции и сортировка
//firstLog(9),firstDate(10),editLog(11),editDate(12),editNum(13) - модерация
//hit(14),votes(15),votessum(16),Comments(17),
public $tabParent;
public $fildsMandatory;//=$tabArticle.".id,".$tabArticle.".webProject,".$tabArticle.".orderBy,".$tabArticle.".inProject,".$tabArticle.".themeDeep,".$tabArticle.".isVis,".$tabArticle.".thisOrigin,".$tabArticle.".isTop,".$tabArticle.".firstLog,".$tabArticle.".firstDate,".$tabArticle.".editLog,".$tabArticle.".editDate,".$tabArticle.".editNum,".$tabArticle.".hit,".$tabArticle.".votes,".$tabArticle.".votessum,".$tabArticle.".comments,";
// блок обязательных полей для любого класса //
///////////////////////////////////////////////

public $langOut;//поле хранящее значение зыка веб-станицы eng/ua/ru
public $langInfoInLink;//поле хранящее данные о языке для ссылки в формате &lang=ua/eng/ru

public function __construct($type,$uid,$lang)
	{
			$this->langOut=$lang;
			$this->langInfoInLink="&lang=".$lang;//инизиализируем поле хранящее данные о языке для ссылки
			$this->linkToUnit="?".$type."=".$uid;//генерируем линк на страницу ВНИМАНИЕ! языковая информация из поля $this->langInfoInLink подключается единожды в месте вызова веб-ссылки
			$this->unic=$uid;
			switch($type){
			case "article":
					$this->typeData="стаття";
					$this->tabParent=Singleton::getInstance()->tabArticle;
					$this->fildsMandatory=$this->tabParent.".id,".$this->tabParent.".webProject,".$this->tabParent.".orderBy,".$this->tabParent.".inProject,".$this->tabParent.".themeDeep,".$this->tabParent.".isVis,".$this->tabParent.".thisOrigin,".$this->tabParent.".isTop,".$this->tabParent.".firstLog,".$this->tabParent.".firstDate,".$this->tabParent.".editLog,".$this->tabParent.".editDate,".$this->tabParent.".editNum,".$this->tabParent.".hit,".$this->tabParent.".votes,".$this->tabParent.".votessum,".$this->tabParent.".comments,";
				////////////////////////////////////////////////////////	
				//генерируем путь к последней обложке журнала/сборника//
				$pos=strpos($uid,'_');//определяем где оканчивается первый сектор юид(а)
				$journalName=substr($uid,0,$pos);//вырезаем первую часть
				$this->pathToImg=$DirOfImage.'JournALL/'.$journalName.'/'.$journalName.'.jpg';//пишем в поле содержащее путь к картинке
				//путь сгенерировали//
				//////////////////////
				//
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//задаем префикс для обеспечения возможностей работы с альтернативными языками													//
				//ВАЖНО: в таблицах баз данных имена полей с альтернативными языками определяются постфиксными указателями типа _eng, _ru и т.п.//
				if($lang=='eng') {$ifAlterLang='_eng';$this->typeData="article";} else $ifAlterLang=''; 
				//постфикс задан//
				//////////////////
				$sqlToBibAndBibJournal="select ".$this->fildsMandatory." nl_bib_journal.unic as unitId,nl_bib_journal.author".$ifAlterLang." as unitMoreInfo,nl_bib_journal.pub".$ifAlterLang." as unitMainInfo,nl_bib_journal.pdfPath,nl_bib_journal.histans,nl_bib_journal.hitpdf,";//поля таблицы статей
	 $sqlToBibAndBibJournal.="nl_bib.nazva".$ifAlterLang." as nazva,nl_bib.year,nl_bib.numName,nl_bib.numStart,nl_bib.numEnd,nl_bib.doubleSign,nl_bib.isbn";//поля таблицы библиографии
	 $sqlToBibAndBibJournal.=" from `nl_bib_journal` LEFT JOIN `nl_bib` ON nl_bib_journal.isbn=nl_bib.isbn where nl_bib_journal.unic='".$uid."'";//оптимизирует запросы в соответствии с принадлежностью ресурса тому или иному проекту
	$selWhereSpace=mysql_query($sqlToBibAndBibJournal) or die(mysql_error()); 
			$replaceInRow=mysql_fetch_array($selWhereSpace);
	 		$this->unic=$replaceInRow['unitId'];
						//////////////////////////////////////////////////////////////////////////////////
						// навигационная строка	для единицы хранения приводимой к article				//
						// формирование строки номера[ов] и части[ей] 									//
						// numName - формат нумерации:№ (по-умолчанию), Т., Ч.							//
						// numStart - первый номер (обязательно арабская цифра)						    //
						// numEnd - диапазон для вариантов нескольких номеров (не обязательный формат)  //
			$this->navField="<table style='width:100%;padding:3px;font-size:14px;'>
					<tr><td style='width:33%;'>".$this->typeData;//тип документа
			if($replaceInRow['pdfPath']!='' || $replaceInRow['histans']!='') $this->navField.="<span style='font-size:12px;' title='повна версія'> &#9685;</span>"; else $this->navField.="<span style='font-size:12px;' title='бібліографічні дані'> &#5628; &#4440; &#9684; </span>";
			$this->navField.="</td><td style='width:33%;'><a href='?litera&askAbout=".$journalName.$this->langInfoInLink."'>".$replaceInRow['nazva']."</a></td><td style='width:33%;'><a href='litera=".$replaceInRow['isbn'].$this->langInfoInLink."'>".$replaceInRow['year'].", ".$replaceInRow['numName']." ".$replaceInRow['numStart'].$replaceInRow['numEnd']." ".$replaceInRow['doublSing']."</a></td></tr></table>";
						// навигационная строка //
						//////////////////////////
			//количество загрузок (актуально только для полнотекстовых версий)
			if($replaceInRow['hitpdf']!='' && $replaceInRow['hitpdf']!=0) $this->hitpdf=$replaceInRow['hitpdf'];
	$this->mainData=$replaceInRow['unitMainInfo'];
	$this->otherData=$replaceInRow['unitMoreInfo'];
//ВАЖНО! предполагается, что на более раннем этапе (при работе с записями в запросе) было проконтролировано наличие английской версии в занной саписи eng_Pub!=''  
				
			break;
			case "litera":
				$this->typeData="бібліографічний опис";
				$selWhereSpace=mysql_select("id as unitId,kat as unitAbout,bib as unitName","nl_bib","id=".$uid);
			break;
			case "news":
				$this->typeData="повідомлення";
				$selWhereSpace=mysql_select("id as unitId,org as unitAbout,main_inf as unitName","uni_news","id=".$uid);
			break;
			case "hist":
				$this->typeData="біобібліографічні дані";
				$selWhereSpace=mysql_select("id as unitId,organization as unitAbout,Fam_1 as unitName","uni_str_sot","id=".$uid);
				break;			
			case "termin":
				$this->typeData="енциклопедична стаття";
				$selWhereSpace=mysql_select("latinka as unitId,author as unitAbout,termin as unitName","uni_termin","latinka=".$uid);
			break;			
			case "link":
				$this->typeData="електронний ресурс";
				$selWhereSpace=mysql_select("unic as unitId,link as unitAbout,nazva_ua as unitName","uni_links","unic=".$uid);
			break;			
			case "hronos":
				$this->typeData="хронологія";
				$selWhereSpace=mysql_select("id as unitId,sourse as unitAbout,text as unitName","uni_hrono","id=".$uid);
			break;			
			default: echo $type." - вказаний тип не існує!";

		}	

	////////////////////////////////////////////////////////////////////////////////////
	// блок формирования основной и вспомогательной записей для альтернативных языков //
	//if($lang=='eng'){
	//	if($replaceInRow['unitAbout']!='') $this->otherData=$replaceInRow['unitAbout']; else $this->otherData=$replaceInRow['unitAbout_mainLang'];
	//	if($replaceInRow['unitName']!='') $this->mainData=$replaceInRow['unitName'];else $this->mainData=$replaceInRow['unitName_mainLang'];
	//}
	//else{
	//$this->otherData=$replaceInRow['unitAbout'];
	//$this->mainData=$replaceInRow['unitName'];
	//}
	////////////////////////
	// УНИВЕРСАЛЬНЫЕ ПОЛЯ //
	/////////////////////////////////////////////////////////////////////
	// блок инициализации универсальных полей для выборки с сортировки //
	$this->id=$replaceInRow['id'];//-1-
	$this->webProject=$replaceInRow['webProject'];//-2-
	$this->orderBy=$replaceInRow['orderBy'];//-3-
	$this->inProject=$replaceInRow['inProject'];//-4-
	$this->themeDeep=$replaceInRow['themeDeep'];//-5-
	$this->isVis=$replaceInRow['isVis'];//-6-
	$this->thisOrigin=$edit_date['thisOrigin'];//-7-
	$this->isTop=$edit_date['isTop'];//-8-
	// блок инициализации универсальных полей для выборки с сортировки //
	/////////////////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////
	// блок инициализации универсальных полей модерации //
	$this->firstLog=$replaceInRow['firstLog'];//-9-
	$this->firstDate=$replaceInRow['firstDate'];//-10-
	$this->editLog=$replaceInRow['editLog'];//-11-
	$this->editDate=$replaceInRow['editDate'];//-12-
	$this->editNum=$replaceInRow['editNum'];//-13-
	// блок инициализации универсальных полей модерации //
	//////////////////////////////////////////////////////
	$this->hit=$replaceInRow['hit'];//-14-
	$this->votes=$replaceInRow['votes'];//-15-
	$this->votessum=$replaceInRow['votessum'];//-16-
	$this->comments=$replaceInRow['comments'];//-17-
	// блок инициализации универсальных полей //
	////////////////////////////////////////////
	}
	/////////////////////////////////////////////////////////
	// виртуальная функция реализуемая в классе-наследнике //
	public function drowBigWeb()
			{
					//генерация и вывод развернутой информации об объекте
			}
	// виртуальная функция реализуемая в классе-наследнике //
	/////////////////////////////////////////////////////////
	//
	//////////////////////////////////////////////////////////////
	// рисование блока информационной единицы при выводе списка //
	public function drowForWeb()//при вызове на конкретном типе расширяется за счет реализации развернутой функции drowBigWeb уникальной для каждого из восьми классов 
	{
	if($this->langOut=='eng') $this->linkToUnit.='&lang=eng';
	echo'	
		<div class="unitBox">';		
			//дата размещения на сайте
				if($this->firstDate!='0000-00-00') echo '<div class="justDate" title="час розміщення на ресурсі"><span style="font-style:normal;"><!--&#8987;-->&#8986; </span>'.$this->firstDate.'</div>';
	echo'
			<!-- навигационная панель (уникальна для каждого из восьми типов) -->';
	//обновляем количество просмотров страниц в случае работе на экземплярах отличных от базового
	if(get_class($this)!='storageUnit')
	{
	$this->hit=($this->hit)+1;
	mysql_update("nl_bib_journal","hit='".$this->hit."'","unic='".$this->unic."'");// else $newHitPosition=$this->hit;
	}
	echo'
			<div class="classHitIn"><span title="кількість переглядів">&#5860; - '.$this->hit.'</span>';
	if($this->hitpdf!='' && $this->hitpdf!=0) echo'<span title="кількість завантажень">/&#5864; '.$this->hitpdf.'</span>';
	echo'</div>
			<div class="classHit">'.$this->navField./*'  - '.$this->typeData.' - данные по контенту * '.$this->hit.*/'
			</div>
			<!-- презентационное изображение -->
			<div class="presentImg">
					<a href="'.$this->linkToUnit.$this->langInfoInLink.'">
						<img class="list_img" src="'.Singleton::getInstance()->histansPath.$this->pathToImg.'" title="'.$this->mainData.'"/>
					</a>
			</div>
			<!-- основная информация: автор, название, характеристики -->
			<div class="mainInf">
					<span class="list_add">
						'.$this->otherData.'
					</span><br/>
					<a class="list_text" href="'.$this->linkToUnit.$this->langInfoInLink.'">'.$this->mainData.'</a>
					</div>
		';
	$this->drowBigWeb();//уникальна для каждого из восьми классов (на базовом классе не существует)
	echo'
		</div>
	';	
	}
	// рисование блока информационной единицы при выводе списка //
	//////////////////////////////////////////////////////////////
}
// storageUnitClass 001//
/////////////////////////
//
//
//
//
//////////////////
// articleClass //
class article extends storageUnit
{
//ініцалізовано в storageUnit
/*
	public $typeData;
	public $mainData;
	public $otherData;
*/

public $SiteLink;
public $h_index;
// для реализации версии на альтернативном языке необходимо соответсвующий суфикс при вызове нужного поля if($this->OutLang=='end') $ifAlterLang='_eng';
public $section;					//public $section_eng;
public $pub;/*=mainData*/			//public $pub_eng;
public $author;/*=otherData*/		//public $author_eng;
public $keywords;					//public $keywords_eng;
public $annotation;					//public $annotation_eng;

public $scopus;
public $histans;
public $refFromThis;
public $refToThis;
public $ex;
public $isbn;
public $pdfPath;
public $swfPath;
public $pgStart;
public $pgEnd;
public $hitpdf;
//поля из родительской таблицы содержащей библиографическое оприсание номера
public $bib;
public $nazva;

public function __construct($uid,$lang)
	{
	 parent::__construct("article",$uid,$lang);
	//ОБРАТИТЬ ВНИМАНИЕ: с помощью скрипта необходимо обнулить поле содержащее готовый линк
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//задаем префикс для обеспечения возможностей работы с альтернативными языками													//
				//ВАЖНО: в таблицах баз данных имена полей с альтернативными языками определяются постфиксными указателями типа _eng, _ru и т.п.//
				if($lang=='eng') {$ifAlterLang='_eng';$this->typeData="article";} else $ifAlterLang=''; 
				//постфикс задан//
				//////////////////
	 $sqlToBibAndBibJournal="select nl_bib_journal.section".$ifAlterLang." as section,nl_bib_journal.pub".$ifAlterLang." as pub,nl_bib_journal.author".$ifAlterLang." as author,nl_bib_journal.keywords".$ifAlterLang." as keywords,nl_bib_journal.annotation".$ifAlterLang." as annotation,nl_bib_journal.scopus,nl_bib_journal.histans,nl_bib_journal.refFromThis,nl_bib_journal.refToThis,nl_bib_journal.pgStart,nl_bib_journal.pgEnd,nl_bib_journal.pdfPath,nl_bib_journal.swfPath,";
	 $sqlToBibAndBibJournal.="nl_bib.ex,nl_bib.isbn,nl_bib.bib".$ifAlterLang." as bib,nl_bib.nazva".$ifAlterLang." as nazva,nl_bib.siteLink";
	 $sqlToBibAndBibJournal.=" from `nl_bib_journal` LEFT JOIN `nl_bib` ON nl_bib_journal.isbn=nl_bib.isbn where nl_bib_journal.unic='".$uid."'";//оптимизирует запросы в соответствии с принадлежностью ресурса тому или иному проекту
	$bigArticle=mysql_query($sqlToBibAndBibJournal) or die(mysql_error()); 
	$bigArticleRow=mysql_fetch_array($bigArticle);
///////////////////////////////////////////////////////////////////////////////////////
//блок наполняемый данными в соответствии с заданным языком программы см.$ifAlterLang//
	$this->section=$bigArticleRow['section'];
	$this->pub=$bigArticleRow['pub'];
	$this->author=$bigArticleRow['author'];
	$this->keywords=$bigArticleRow['keywords'];
	$this->annotation=$bigArticleRow['annotation'];
	$this->scopus=$bigArticleRow['scopus'];
	$this->histans=$bigArticleRow['histans'];
	$this->refFromThis=$bigArticleRow['refFromThis'];
	$this->refToThis=$bigArticleRow['refToThis'];
	$this->isbn=$bigArticleRow['isbn'];
	$this->pdfPath=$bigArticleRow['pdfPath'];
	$this->swfPath=$bigArticleRow['swfPath'];
	$this->pgStart=$bigArticleRow['pgStart'];
	$this->pgEnd=$bigArticleRow['pgEnd'];
	$this->hitpdf=$bigArticleRow['hitpdf'];
//поля из родительской таблицы содержащей библиографическое оприсание номера
//public $ex;
	$this->bib=$bigArticleRow['bib'];
	$this->nazva=$bigArticleRow['nazva'];
	
/*
public $scopus;
public $histans;
public $citatFromThis;
public $citatToThis;

public $isbn;
public $href;
public $path;
public $pgStart;
public $pgEnd;
public $hitpdf;
//поля из родительской таблицы содержащей библиографическое оприсание номера
//public $ex;
public $bib;
public $nazva;*/
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// функция вывода развернутой информации о статье [включена в базовую функцию drowForWeb() - дорисовывает данные библиографии, проектов, рейтинги и комментарии]//
	public function drowBigWeb()
	{
if($this->annotation!='') echo '<span style="padding-left:35px;" title="aнотація">&#9636; </span>'.$this->annotation;	
//if($this->keywords!='') echo 'Ключові слова:'.$this->keywords.'<br/>';
	
			echo '<span style="padding-left:35px;" title="бібліографічне посилання">&#9758; </span><p id="description" style="margin-top:-1px;" class="text_bib_link">'.$this->otherData.' '.$this->mainData.' [Електронний ресурс] // '.$this->bib.' – Режим доступу:'.Singleton::getInstance()->projectPath.$this->linkToUnit.' (останній перегляд: '.date("d-m-Y").').</p><div style="padding-top:-20px;padding-bottom:20px;text-align:right;"><input type="button" id="copy-button" value="&#9745;" title="cкопіювати бібліографічне посилання в буфер обміну"/></div>';
	///////////////////////////////////////////////////////////////////
	// проверка и генерация путей к проекту и подтемам (при наличии) //
	if($this->langOut=='eng') $ifAlterLang='_eng';
		$InPro='';
		$themeDeep='';
		if($this->themeDeep!='') {
		$arrTheme=explode(',',$this->themeDeep);
		$isFirstIteration=true;$queryDeep='';
		foreach($arrTheme as $currTheme) {
											if($isFirstIteration){$queryDeep.="AND (theme_number=".$currTheme;$isFirstIteration=false;}
											else $queryDeep.=" OR theme_number=".$currTheme;
										}
		if($queryDeep!='')$queryDeep.=") ";							
							$query_art = "	SELECT uni_project.nazva".$ifAlterLang." as nazva,out_key,InProject,theme_deep".$ifAlterLang." as theme_deep,theme_number FROM uni_project
											INNER JOIN uni_project_deep
											ON uni_project.out_key=uni_project_deep.InProject
											WHERE InProject='".$this->inProject."' $queryDeep";

							//$query_LiberForSotFam = "SELECT id FROM uni_str_sot WHERE Fam_short LIKE '$onlyOneAvtor%'";
							$result_LiberForSotFam = mysql_query($query_art) or die("Query failed");
							$firstStep=false;
							while($onebyoneAvtor=mysql_fetch_array($result_LiberForSotFam))
							{	
							$InPro='<a href="'.Singleton::getInstance()->projectPath.'?article=byTheme'.$this->langInfoInLink.'">'.$onebyoneAvtor['nazva'].'</a><br/>';
							if($firstStep)$themeDeep.=', ';else $firstStep=true;
							$themeDeep.='<a href="'.Singleton::getInstance()->projectPath.'?article&deep='.$onebyoneAvtor['theme_number'].$this->langInfoInLink.'">'.$onebyoneAvtor['theme_deep'].'</a>';
							}
	}
	// проверка и генерация путей к проекту и подтемам (при наличии) //
	///////////////////////////////////////////////////////////////////
	//
	////////////////////////////////////////////////////////////////
	// при отсутствии ключевых слов они должны быть сгенерированы //
		if($this->keywords!='') $keys=$this->keywords;
		else {$wrdsForSplit=escComa($this->mainData); $keys=explode(' ',$wrdsForSplit);}
		$onlyFirstFive=0;
		foreach($keys as $currKeyWord) {
				if(strlen($currKeyWord)>6 && $onlyFirstFive<5){
						$keyWords.='<a href="zzz='.$currKeyWord.'">'.$currKeyWord.'</a>, ';$onlyFirstFive++;}
		}

	// при отсутствии ключевых слов они должны быть сгенерированы //	
   	// 	////////////////////////////////////////////////////////////////
	
		echo'
<!--	<div class="unitBox"> контейнер-обертку оптимально использовать единожды фрагменты классов-наследников вставляя через кеш-->
			<!-- тематика документа, количество просмотров, рейтинговая информация -->
			<div class="classHit">
			<table style="width:100%;padding=10px;">
			<tr>	<td style="width:33%;"><span title="ключові слова">&#2000;</span></td>
				<td style="width:33%;"><span title="залучена до проектів">&#1769;</span></td>
				<td style="width:33%;"><span title="включена до підрозділів">&#8258;</span></td>	
			</tr>
			<tr>	<td style="width:33%;">'.$keyWords.'</td>
				<td style="width:33%;">'.$InPro.'</td>
				<td style="width:33%;">'.$themeDeep.'</td>	
			</tr>
			</table>
			</div>
<!--	</div> контейнер-обертку оптимально использовать единожды фрагменты классов-наследников вставляя через кеш-->
';
echo '
			<div class="presentImg">';
					//<!-- пробуем найти фото автора-->
					if($this->langOut=='eng') $imgFor=avtorDrowEng($this->otherData);
					else $imgFor=avtorDrow($this->otherData);
					if($imgFor!='') echo $imgFor;
						else {
						
							echo '<img class="list_img" src="'.$DirOfImage.$this->pathToImg.'" title="'.$this->mainData.'"/>';
							}
	echo'
		</div>
			<!-- основная информация: автор, название, характеристики -->
			<div class="mainInf">';

				$pathForCheckSWF=Singleton::getInstance()->innerPath.$this->swfPath;
					if(file_exists($pathForCheckSWF)) echo '<object type="application/x-shockwave-flash" data="'.Singleton::getInstance()->histansPath.$this->swfPath.'" height="670" width="480"></object>';else 'ups';
	
				$pathForCheck=Singleton::getInstance()->innerPath.$this->pdfPath;
	if(file_exists($pathForCheck))	echo '<br/><a href="'.Singleton::getInstance()->histansPath.$this->pdfPath.'" target="_blank"><img src="'.Singleton::getInstance()->histansPath.'LiberUA/pdf.gif" width="70" alt="pdf-file '.$this->mainData.'"/><span title="отримати pdf-файл">&#9196;</span></a><br/>';else 'nea';
;
	echo'	
			</div>';
						echo '<div class="classHit">
								<table style="width:100%;padding=10px;">
								<table style="width:100%;padding=10px;">
			<tr>	<td style="width:30%;"><span title="бібліографія статті в базі HistANS">&#8690;</span></td>
				<td style="width:30%;"><span title="тексти бази Histans з посиланнями на статтю">&#8689;</span></td>
				<td style="width:40%;"><span title="рейтинг">&#9734;&#9734;&#9734;</span></td>	
			</tr>
			<tr>	
				<td style="width:30%;"></td>
				<td style="width:30%;"></td>
				<td style="width:40%;">
<span id="forvote">';
				if($this->votes!=0){ $resvotes=$this->votessum/$this->votes; $rating=round($resvotes, 2); echo $rating.' ('.$this->votessum.'/'.$this->votes.')';}




				else echo ' +++ ';
				echo '</span>
				<input type="hidden" id="idVal" value="'.$this->unic.'"/>
			<select id="proandcontra"><option value="0" selected="selected">
			Ваша оцінка:
			</option><option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			</select>
			<span id="volButton"><input id="vote" type="submit" value="Голосувати!"/></span>
			</td>	
			</tr>
			</table>
			</div>';
				////////////
				// скрипт обработки результатов голосования //
?>
<script language="javascript">
$(document).ready(function(){

	//inProject
	$("#vote").click(function(){
	
	//	var stringVal=this;
	//alert($("#prjectName").val()+' '+$("#kat").val+' You click it!!!'+stringVal.value);
	

	//var resId=stringVal.substring(19,stringVal.length);
	
	var votenum=$("#proandcontra").val();
	alert(votenum+'!!!');
	var idVal=$("#idVal").val();
	//alert(votenum+' '+idVal);
	
	//$.cookie("votesets", "777");
	//var tmp=$.cookie("votesets");
   	//if(tmp==‘collapsed’) alert("set");
	//alert("set"+tmp);
	//var cookiev=$.cookie("draggableDiv");
	//alert('Рейтінг оновлено!!');
		if(votenum!=0)
		{
			$.ajax({
						type: "GET",
						url: "article_request.php",
						data: "voteit="+votenum+"&idVal="+idVal,
						success: function (data, textStatus)
						{
							
							alert('Рейтінг оновлено!');
							$("#forvote").html(data);
							$("#volButton").html('<input class="vote" disabled="disabled" type="submit" value="Голосувати!"/>');
						}
			});
		}
	});

});
</script>
<?php

/*

			<tr>	
				<td style="width:20%;">	'.$hits.'</td>
				<td style="width:80%;"><span title="рейтінг">; </span>></td>
			</tr>
			</table></div>';
 */
					//!!!!!!!!!!!!!!!!!!!!	echo '<div class="fb-comments" data-href="'.$mainDir.'?article='.$this->unic.'" data-width="470" data-num-posts="10"></div></div>';
					
	}

	///////////////////////////////////////
	// рисование блока для вывода статьи //
	public function outArticleForArrey()
	{

	/*
	SELECT nazva,out_key,InProject,theme_deep FROM uni_project
										INNER JOIN uni_project_deep
										ON uni_project.out_key=uni_project_deep.InProject*/
	
	///////////////////////////////////////////////////////////////////
	// проверка и генерация путей к проекту и подтемам (при наличии) //
		$InPro='';
		$themeDeep='';
		if($this->themeDeep!='') {
		$arrTheme=explode(',',$this->themeDeep);
		$isFirstIteration=true;$queryDeep='';
		foreach($arrTheme as $currTheme) {
											if($isFirstIteration){$queryDeep.="AND (theme_number=".$currTheme;$isFirstIteration=false;}
											else $queryDeep.=" OR theme_number=".$currTheme;
										}
		if($queryDeep!='')$queryDeep.=") ";							
							$query_art = "	SELECT nazva,out_key,InProject,theme_deep,theme_number FROM uni_project
											INNER JOIN uni_project_deep
											ON uni_project.out_key=uni_project_deep.InProject
											WHERE InProject='".$this->inProject."' $queryDeep";

							//$query_LiberForSotFam = "SELECT id FROM uni_str_sot WHERE Fam_short LIKE '$onlyOneAvtor%'";
							$result_LiberForSotFam = mysql_query($query_art) or die("Query failed");
							while($onebyoneAvtor=mysql_fetch_array($result_LiberForSotFam))
							{	
								$InPro='<a href="'.Singleton::getInstnce()->histansPath.'?article=byTheme">'.$onebyoneAvtor['nazva'].'</a><br/>';
								$themeDeep.='<a href="'.Singleton::getInstnce()->histansPath.'?article&deep='.$onebyoneAvtor['theme_number'].'">'.$onebyoneAvtor['theme_deep'].'</a><br/>';
							}
		}
	// проверка и генерация путей к проекту и подтемам (при наличии) //
	///////////////////////////////////////////////////////////////////	 
	
	echo'	
		<div class="unitBox">		
			<!-- дата размещения на сайте-->
			<div class="justDate">
							';
				if($this->first_add_date!='0000-00-00') echo 'на Порталі з '.$this->first_add_date;
	echo'
			</div>
			<!-- тематика документа, количество просмотров, рейтинговая информация -->
			<div class="classHit">
			<table style="width:100%;padding=10px;">
			<tr>	<td style="width:33%;">'.$this->typeData.' з журнала\збірника:</td>
				<td style="width:33%;">залучена до проектів:</td>
				<td style="width:33%;">включена до підрозділів:</td>	
			</tr>
			<tr>	<td style="width:33%;"><a href="?litera&askAbout='.$this->seria_name.'">'.$this->book.'</a></td>
				<td style="width:33%;">'.$InPro.'</td>
				<td style="width:33%;">'.$themeDeep.'</td>	
			</tr>
			</table>
			</div>
			<!-- презентационное изображение -->
			<div class="presentImg">';
					//<!-- пробуем найти фото автора-->
					if($this->langOut=='eng') $imgFor=avtorDrowEng($this->otherData);
					else $imgFor=avtorDrow($this->otherData);
					if($imgFor!='') echo $imgFor;
						else {
						
							echo '<img class="list_img" src="'.$DirOfImage.$this->pathToImg.'" title="'.$this->mainData.'"/>';
							}
	echo'
		</div>
			<!-- основная информация: автор, название, характеристики -->
			<div class="mainInf">
					<span class="list_add">
						'.$this->otherData.'
					</span><br/>
					<span class="list_text">'.$this->mainData.'</span>';
					if($this->annotation!='') echo '<blockquote>'.$this->annotation.'</blockquote>';
					echo '<hr/>';

					$pathForCheckSWF=$innerPath.$this->path;


					if(file_exists($pathForCheckSWF)) echo'

					<object type="application/x-shockwave-flash" data="'.$histansPath.$this->path.'" height="670" width="480"></object>
		';
	
					$pathForCheck=$innerPath.$this->href;
	if(file_exists($pathForCheck))	echo '<br/><a href="'.$histansPath.$this->href.'" target="_blank"><img src="'.$histansPath.'LiberUA/pdf.gif" width="70"     alt="pdf-file '.$this->mainData.'"/><i>Отримати pdf-файл</a><br/>';
	echo'	
			</div>';

	// fb start

//!!!!!!!!!!!!!!!!!!!!	echo '<div class="fb-comments" data-href="'.$mainDir.'?article='.$this->unic.'" data-width="470" data-num-posts="10"></div></div>';
	
	/////
	echo '
		</div>
	';



		/////////////////////////////////////////////
		// обновление количества просмотров статьи //
		$hits=$this->hit;
		$hits++;
			mysql_update("uni_bib_journal","hit='".$hits."'","unic='".$this->unic."'");
			//if (mysql_affected_rows()==1){
			//			echo $hitsPdf;
			//	}
			//	else
			//	echo 'ups!';
			//  }
	//////////////////////
	// рассчет рейтинга //
	
	
	echo '<div class="classHit">
		<table style="width:100%;padding=10px;">
			<tr>	
				<td style="width:20%;">Переглядів:				'.$hits.'</td>
				<td style="width:80%;">Рейтінг: <b><span id="forvote">';
				if($this->votes!=0){ $resvotes=$this->votessum/$this->votes; $rating=round($resvotes, 2); echo $rating.' ('.$this->votessum.'/'.$this->votes.')';}
				else echo ' +++ ';
				echo '</span>
				<input type="hidden" id="idVal" value="'.$this->unic.'"/>
			<select id="proandcontra"><option value="0" selected="selected">
			Ваша оцінка:
			</option><option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			</select>
			<span id="volButton"><input id="vote" type="submit" value="Голосувати!"/></span></td>
			</tr>
	</table></div>';
	////////	
	}
	// рисование блока для вывода статьи //
	///////////////////////////////////////
}
// articleClass //
//////////////////
//what is it
?>

