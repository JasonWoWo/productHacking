<?php
/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/8/1
 * Time: 下午3:07
 */

namespace QueryCollection;

require __DIR__ . '/../Bootstrap.php';

use MiddlewareSpace\UnCoreUseAuthorize;

class QueryUserContactInfo extends UnCoreUseAuthorize
{
    public function main()
    {
        $currentDate = new \DateTime(date('Y-m-d'));
        $currentDate->modify('-1 day');
        $this->getUnCoreUser($currentDate->getTimestamp(), 0, -1, 5);
        $this->getAuthorizeStatus();
        $detailItems = $this->userDetailItem;
        foreach ($detailItems as $item) {
            echo sprintf("%s;%s;%s;%s;%s \n", $item['udid'], $item['uid'], $item['max_bct'], $item['contactAuth'], $item['channelId']);
        }
    }
    
    public function getUserId()
    {
        $phoneItems = $this->getPhoneItems();
        $this->geTmpUserId($phoneItems);
    }
    
    public function getPhoneItems()
    {
        return array(
            '18747972891',
            '13754634847',
            '15603653963',
            '13526901306',
            '15766000507',
            '18512430463',
            '13467736832',
            '13920980687',
            '13320827870',
            '18612253442',
            '15857449576',
            '15803584727',
            '18609711477',
            '18643445656',
            '13596092077',
            '15173663334',
            '15573976944',
            '18200654695',
            '15019905840',
            '15201278380',
            '13506071482',
            '18949804636',
            '13778550825',
            '18582263849',
            '13951214007',
            '15866556250',
            '18888155863',
            '18103919044',
            '13962689175',
            '13219519746',
            '15207548568',
            '18729257760',
            '18435210210',
            '15729353900',
            '15697168661',
            '18907340178',
            '13954035752',
            '15071124918',
            '13545696346',
            '13726923234',
            '15082145013',
            '13727001788',
            '13599665580',
            '18307206476',
            '15622830056',
            '18701389625',
            '18832537135',
            '18212975200',
            '13369168582',
            '17710756681',
            '15188341724',
            '18750916004',
            '13684042455',
            '13758167261',
            '15209364709',
            '15296002410',
            '13828497764',
            '18913853685',
            '15529251936',
            '15068347729',
            '18098010926',
            '13951871208',
            '13687222705',
            '13077324268',
            '15919561545',
            '13985441871',
            '18375863561',
            '15762541278',
            '15803955645',
            '18903901192',
            '18762033922',
            '18835134691',
            '13637406618',
            '15273644258',
            '17778195525',
            '15814033385',
            '18383276841',
            '15921980232',
            '15173201214',
            '15871705285',
            '18234443713',
            '18246593487',
            '18738837725',
            '13266576782',
            '13996310825',
            '13574416823',
            '15857188565',
            '18622527603',
            '15761390110',
            '15840580395',
            '13633528535',
            '18312006515',
            '13115244333',
            '18328150702',
            '13969238952',
            '15381753380',
            '15273339660',
            '13576959127',
            '15017220836',
            '13001107800',
            '15983266335',
            '13972527435',
            '13130043119',
            '18374604096',
            '15245645873',
            '18872362656',
            '18629915915',
            '18698624968',
            '18601366060',
            '15356863131',
            '13426363218',
            '13297943158',
            '13336466633',
            '18712316996',
            '18645980016',
            '18374157130',
            '13852729057',
            '15028647520',
            '17805052845',
            '18865502377',
            '13684532217',
            '18660144418',
            '13688675119',
            '13923002340',
            '13942967557',
            '15977867655',
            '15927783526',
            '13399804161',
            '18213095743',
            '15283171033',
            '18230276930',
            '15675397198',
            '15093338198',
            '13787949679',
            '13132131313',
            '13994338566',
            '13779983824',
            '13554308728',
            '15991987739',
            '13892868389',
            '13836873152',
            '18380406498',
            '18273169816',
            '13276737571',
            '17758885942',
            '13777466065',
            '15732118258',
            '13761022457',
            '13602019415',
            '18633510830',
            '17780919993',
            '18983532865',
            '13013559037',
            '18859926977',
            '13401499728',
            '18955572287',
            '13209431465',
            '15847718162',
            '15234035971',
            '14747281233',
            '18748138020',
            '15280233214',
            '13792981231',
            '15767971194',
            '13034802935',
            '17768598627',
            '18707619609',
            '13902678604',
            '18282792722',
            '15826267563',
            '18273981340',
            '13308101526',
            '15022730210',
            '18686408587',
            '13861514598',
            '18846179799',
            '18108121291',
            '15088133340',
            '15671577537',
            '13530612312',
            '15827629634',
            '18731800304',
            '13439089616',
            '18894800176',
            '18252238091',
            '18742048848',
            '18133295801',
            '15036012452',
            '15150463500',
            '15108611905',
            '18569369315',
            '15897679659',
            '13719583004',
            '18518036626',
            '13527971574',
            '15897786147',
            '13656220685',
            '13523246733',
            '15810539323',
            '13245543080',
            '13805055544',
            '15601384250',
            '18827040692',
            '15767512057',
            '13555770732',
            '13558822803',
            '13750051936',
            '18679091246',
            '18138804045',
            '18784588315',
            '18802519599',
            '18630100920',
            '18604121910',
            '18581123665',
            '13565057983',
            '15810656845',
            '13615112012',
            '13854256653',
            '18318585008',
            '13561193535',
            '13920911633',
            '15575594805',
            '15223259964',
            '13683515609',
            '18074477122',
            '18750036080',
            '15914321119',
            '15979177727',
            '18659183612',
            '14795593119',
            '15733229775',
            '18850400470',
            '13760286288',
            '18202278852',
            '18510249599',
            '13941153347',
            '15986023878',
            '18328143205',
            '18813187905',
            '13925831214',
            '15015069703',
            '15001110302',
            '15802378235',
            '13804154857',
            '18366115013',
            '18734342303',
            '18113341631',
            '15651953022',
            '15044919633',
            '15159764527',
            '13616767630',
            '15813396160',
            '15899862410',
            '15943191305',
            '13990353125',
            '13221998675',
            '13261468527',
            '13105538195',
            '13801200559',
            '18349691652',
            '13970193806',
            '13585752810',
            '18226634406',
            '18388299808',
            '13710847414',
            '15102769571',
            '13549184607',
            '18084874842',
            '18879605876',
            '15057220820',
            '18091110093',
            '13692349353',
            '15040224002',
            '13335776906',
            '18222570206',
            '13034901668',
            '18184486022',
            '17720793488',
            '13477447966',
            '15281902312',
            '13912901315',
            '18697737056',
            '18188327976',
            '15186672215',
            '15267076764',
            '13871266000',
            '15274387046',
            '13426303586',
            '18900780887',
            '13023963101',
            '17854204309',
            '18803923925',
            '18390990454',
            '13617328576',
            '13821997846',
            '18734405299',
            '18607109678',
            '18866157273',
            '15917467480',
            '13982930699',
            '18428309946',
            '13716700646',
            '13811135296',
            '18181152231',
            '15010652775',
            '15139647981',
            '15571777175',
            '15731425559',
            '15927466516',
            '17755614578',
            '18270945821',
            '13968607837',
            '18234716831',
            '15951664308',
            '13903535561',
            '18313312740',
            '13751175503',
            '13667446433',
            '13681421946',
            '18652295189',
            '18501088565',
            '15640988506',
            '13522492161',
            '18383087831',
            '18017755080',
            '13536291148',
            '18947879449',
            '18826124835',
            '18628160998',
            '13986510138',
            '18824865037',
            '18991305540',
            '15800976436',
            '18716646815',
            '18873655808',
            '13738781670',
            '15243645583',
            '13764819490',
            '13522361148',
            '15874521305',
            '15237651171',
            '15659149278',
            '18863111767',
            '17711736563',
            '13929273097',
            '18234863671',
            '13176537569',
            '15032364544',
            '18673252721',
            '13168830903',
            '15213463208',
            '18672030889',
            '13723499962',
            '13007471084',
            '18191760040',
            '18724373614',
            '15552750070',
            '15779836212',
            '15110987776',
            '15843258890',
            '13120723527',
            '18171759822',
            '18625995817',
            '15113482208',
            '15938788781',
            '13378261972',
            '15750545680',
            '15171694520',
            '15983309970',
            '13718060361',
            '18176004567',
            '15058793630',
            '13068358247',
            '13577999917',
            '18784111695',
            '13819237714',
            '13519615391',
            '13600595092',
            '18013536029',
            '13615441608',
            '15961133598',
            '18283007682',
            '15976511168',
            '15828453530',
            '15026622975',
            '17737263063',
            '15396937775',
            '13970255284',
            '15778455997',
            '13725959182',
            '18357217858',
            '15219575804',
            '13297721467',
            '13414993599',
            '18291083553',
            '18234429566',
            '13723560310',
            '15819868068',
            '18604056750',
            '13347450932',
            '13982088864',
            '13925745455',
            '18661910015',
            '18336000255',
            '13626382805',
            '15011034725',
            '15736889223',
            '15211103213',
            '18190137522',
            '18702830089',
            '18723370552',
            '15280883821',
            '15907017590',
            '13917657120',
            '18221149230',
            '13908722975',
            '18180430086',
            '15640150187',
            '15727245771',
            '15149437289',
            '15172853962',
            '13405763108',
            '13808401003',
            '18721592980',
            '17701618903',
            '13988733868',
            '18434554585',
            '18283530579',
            '13691619903',
            '15681154759',
            '18752002190',
            '18975610536',
            '18538608516',
            '15110504867',
            '18808170589',
            '18726867062',
            '15136260006',
            '17726600656',
            '15910257607',
            '15105565708',
            '18831490917',
            '15816835772',
            '13139229733',
            '13753315794',
            '18299099183',
            '15969553957',
            '15621005926',
            '13073028051',
            '15261059237',
            '13599398015',
            '15884540429',
            '18798200864',
            '13544260359',
            '18273042858',
            '13149519931',
            '15680756589',
            '15934140202',
            '17835422115',
            '18200779395',
            '15290809002',
            '18312886088',
            '13876827105',
            '13465872856',
            '18001202293',
            '13169410289',
            '18630656465',
            '15191330876',
            '15907175173',
            '15151821542',
            '13869299233',
            '18716716772',
            '13620973399',
            '13963026454',
            '18366881160',
            '18634642812',
            '13861514598',
            '18409626016',
            '13313990660',
            '15095921960',
            '15915102845',
            '15567521802',
            '15006632913',
            '18857802511',
            '15065632031',
            '18505583120',
            '18035118719',
            '15082705708',
            '18932626654',
            '18048114908',
            '13589166088',
            '15289666161',
            '13271926253',
            '18208014157',
            '17862890107',
            '15993001922',
            '18681730993',
            '13012487549',
            '13308103315',
            '18389730827',
            '13439705311',
            '15387006669',
            '15882412554',
            '15090751307',
            '15701541476',
            '15675393912',
            '15222353203',
            '13812499076',
            '18217659305',
            '13910854693',
            '13889286992',
            '15006966287',
            '13951905531',
            '18520230530',
            '13760769377',
            '18736329309',
            '18190128305',
            '13430811804',
            '13810671984',
            '15766000507',
            '15170243297',
            '18781649299',
            '13669610885',
            '13964362736',
            '15911148514',
            '18039098010',
            '13223463765',
            '13554557311',
            '13558779651',
            '15245624620',
            '17096029771',
            '15725326840',
            '18852958772',
            '15759208170',
            '18810505776',
            '15060110732',
            '13909645599',
            '18664390251',
            '13074486641',
            '15041992589',
            '17704191638',
            '17708490512',
            '15191430315',
            '15310801130',
            '18320227176',
            '15986248554',
            '13546465627',
            '15639439682',
            '13896763365',
            '15534668830',
            '18673183098',
            '15819135785',
            '13571077809',
            '13819023215',
            '18259859905',
            '15527294481',
            '13601002493',
            '18769638867',
            '13842001835',
            '18885083893',
            '18893918284',
            '18090186855',
            '15990101227',
            '13928899410',
            '15615957931',
            '15087161209',
            '18291881845',
            '13349909429',
            '15604341803',
            '13948416164',
            '18204350110',
            '18181539959',
            '15088358965',
            '15023628514',
            '13834840006',
            '13225809863',
            '13128437651',
            '13996859920',
            '18477167291',
            '13225809863',
            '18638872106',
            '13675272518',
            '13550974462',
            '13721193999',
            '15075466526',
            '18829221084',
            '18297889136',
            '15880783916',
            '13847747899',
            '15656067787',
            '15025402524',
            '15652524768',
            '18618149129',
            '13187061405',
            '18907408570',
            '18219181926',
            '13957142930',
            '13559789882',
            '13778911293',
            '13485075237',
            '18729257760',
            '18558931109',
            '13717833340',
            '18774878193',
            '15194229737',
            '13967399845',
            '13701274773',
            '13716155300',
            '15003576718',
            '13051669316',
            '18170979797',
            '15947360659',
            '13998145448',
            '18222831217',
            '15680507139',
            '13920777826',
            '13347343543',
            '13153986205',
            '13581720156',
            '13839907601',
            '13428948551',
            '18311563304',
            '15055553432',
            '15546609365',
            '18308137668',
            '15065748188',
            '15230938532',
            '15106879119',
            '13977976957',
            '13434446570',
            '18711047098',
            '18862878035',
            '13704834450',
            '15928874682',
            '15606310456',
            '13803646970',
            '13670400148',
            '18203790725',
            '15823846867',
            '15001877481',
            '18737863278',
            '15160076130',
            '13126876864',
            '13217400926',
            '18240091770',
            '18347133133',
            '15809618249',
            '18890689365',
            '13664252421',
            '18268763432',
            '13350505998',
            '13033659992',
            '18654646007',
            '15732120361',
            '15937156221',
            '13027503730',
            '18094005737',
            '18258219279',
            '13466695009',
            '13116186299',
            '15637418192',
            '15150735155',
            '15837227357',
            '15023011446',
            '18518480229',
            '13848845220',
            '18003917880',
            '13462414344',
            '18307795535',
            '13697288668',
            '17759615886',
            '15140920003',
            '13787905277',
            '18872587566',
            '18859309070',
            '18665116335',
            '13222053602',
            '13251370860',
            '13659058784',
            '13822132927',
            '15292090110',
            '15726980239',
            '15500388020',
            '15229299763',
            '13676124029',
            '13622333355',
            '18359782690',
            '13810673729',
            '13727589789',
            '15906581636',
            '13971921213',
            '13774191518',
            '15975888421',
            '13454736721',
            '18650430017',
            '18738165831',
            '18505933405',
            '18975566479',
            '15828317843',
            '15068431223',
            '18810443219',
            '13910083870',
            '15716432750',
            '13290241304',
            '18202486723',
            '13379615538',
            '15763635860',
            '18389259616',
            '18814373747',
            '18030315113',
            '15991150415',
            '13224685836',
            '13261777881',
            '13916796568',
            '15276685533',
            '13436620610',
            '13472504255',
            '15911104821',
            '13068683615',
            '13292588860',
            '13517732909',
            '15986054254',
            '15205805874',
            '18820779644',
            '13920911633',
            '13810389391',
            '15001026917',
            '15154370130',
            '15528078152',
            '18602007901',
            '15185957175',
            '13401499728',
            '18650529297',
            '13382625198',
            '13608584643',
            '13555269137',
            '18610229058',
            '15874329902',
            '13331628353',
            '18251155126',
            '18267196676',
            '17828261895',
            '18925024189',
            '15537170060',
            '18780675743',
            '13854256653',
            '15234012810',
            '18600310036',
            '18181645338',
            '15946056566',
            '13516780203',
            '15026817221',
            '13154396306',
            '15518360106',
            '15022730210',
            '18689933134',
            '15847477764',
            '18384861135',
            '15665566629',
            '15111194311',
            '13148798571',
            '15239203925',
            '18539927750',
            '15954971005',
            '15304036993',
            '15504398709',
            '15025185560',
            '15070857065',
            '18036720075',
            '18186402017',
            '15035421313',
            '15849858752',
            '15919432667',
            '18008490063',
            '18844192153',
            '18463584539',
            '13907728133',
            '18610726879',
            '13592501747',
            '15675586084',
            '13634407858',
            '15246292107',
            '18913853685',
            '13590974374',
            '18241421567',
            '13206589605',
            '15376916916',
            '17704028860',
            '13335372333',
            '15809296653',
            '13661536167',
            '18161291003',
            '18860972163',
            '18711624957',
            '15204048733',
            '15874853630',
            '18319871950',
            '18280137539',
            '13346082718',
            '15528025160',
            '15812540199',
            '15620520913',
            '15169617725',
            '15262640456',
            '18603648058',
            '13650140279',
            '18150895283',
            '18279493152',
            '13730157945',
            '18782960958',
            '15141228705',
            '15251718867',
            '15192521179',
            '15856869582',
            '18738837725',
            '13316355522',
            '13315713975',
            '18228395828',
            '13946258688',
            '18101806337',
            '13972527435',
            '15927027528',
            '18747789003',
            '13379290680',
            '18701088857',
            '15233883219',
            '15838134855',
            '13798608242',
            '15736435089',
            '18563521521',
            '15286629064',
            '15179339366',
            '15319088491',
            '18084034250',
            '15866556250',
            '13029135861',
            '15520108116',
            '15325988112',
            '15607085262',
            '18702537145',
            '13651770194',
            '13910515523',
            '15764415578',
            '13331886185',
            '13614148322',
            '13793660350',
            '13601992157',
            '18229940351',
            '13930963537',
            '18383262039',
            '18024069997',
            '13866706160',
            '15894599362',
            '13684042455',
            '13934081152',
            '18435113814',
            '13901707167',
            '18731800304',
            '15691793512',
            '15767512057',
            '15897786147',
            '13357818182',
            '13979953337',
            '13882735516',
            '13938902621',
            '13890793922',
            '18600536931',
            '13896586869',
            '15751510760',
            '18973888629',
            '13860810289',
            '15052619525',
            '13880231597',
            '15093338198',
            '18024116306',
            '15758340418',
            '18738721677',
            '13512266559',
            '18275401962',
            '18255777970',
            '18310730210',
            '13816126383',
            '15152335064',
            '18279273770',
            '15947975656',
            '13891858868',
            '15046748956',
            '13986562920',
            '18655127566',
            '18647541357',
            '13811828335',
            '13565837556',
            '13923498055',
            '13438835297',
            '15167861022',
            '18645802310',
            '13850973203',
            '15132907718',
            '15120024625',
            '15074337644',
            '13596535527',
            '13276737571',
            '18390839464',
            '13205875598',
            '15081827465',
            '18535829408',
            '18645116614',
            '15220960111',
            '18932758871',
            '15981582495',
            '15203044259',
            '15962652189',
            '13851897194',
            '15549489622',
            '13908611866',
            '13782901327',
            '13699563261',
            '13022080501',
            '13600367973',
            '15946165432',
            '13395719828',
            '18375892205',
            '13403731824',
            '13991974969',
            '18329169358',
            '18204336331',
            '18647541357',
            '13835177641',
            '13637498149',
            '15133133617',
            '18373821824',
            '17732165515',
            '18630535068',
            '18370959178',
            '15736858865',
            '18080868770',
            '18409626016',
            '15620691286',
            '18304356210',
            '18760789991',
            '18222117636',
            '18273001277',
            '13611263057',
            '13280028397',
            '15366985291',
            '18519850236',
        );
    }
}
$userContact = new QueryUserContactInfo();
//$userContact->main();
$userContact->getUserId();