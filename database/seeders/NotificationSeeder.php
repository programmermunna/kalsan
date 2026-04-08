<?php

namespace Database\Seeders;

use App\Models\NotificationTemplateLangs;
use App\Models\NotificationTemplates;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notifications = [
            'new_lead'=>'New Lead',
            'lead_to_deal_conversion'=>'Lead to Deal Conversion',
            'new_project'=>'New Project',
            'task_stage_updated'=>'Task Stage Updated',
            'new_deal'=>'New Deal',
            'new_contract'=>'New Contract',
            'new_task'=>'New Task',
            'new_task_comment'=>'New Task Comment',
            'new_monthly_payslip'=>'New Monthly Payslip',
            'new_announcement'=>'New Announcement',
            'new_support_ticket'=>'New Support Ticket',
            'new_meeting'=>'New Meeting',
            'new_award'=>'New Award',
            'new_holiday'=>'New Holiday',
            'new_event'=>'New Event',
            'new_company_policy'=>'New Company Policy',
            'new_invoice'=>'New Invoice',
            'new_bill'=>'New Bill',
            'new_budget'=>'New Budget',
            'new_revenue'=>'New Revenue',
            'new_invoice_payment'=>'New Invoice Payment',
            'new_customer'=>'New Customer',
            'new_vendor'=>'New Vendor',
            'new_proposal'=>'New Proposal',
            'bill_payment'=>'New Payment',
            'invoice_payment_reminder'=>'Invoice Payment Reminder',
        ];


        $defaultTemplate = [
            'notification' => [
                'new_lead' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Lead Name": "lead_name",
                    "Lead Email": "lead_email"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء عميل محتمل جديد بواسطة {user_name}',
                        'zh' => '{user_name} 创建的新商机',
                        'da' => 'Neuer Lead erstellt von {user_name}',
                        'de' => 'Ny kundeemne oprettet af {user_name}',
                        'en' => 'New Lead created by {user_name}',
                        'es' => 'Nuevo cliente potencial creado por {user_name}',
                        'fr' => 'Nouveau prospect créé par {user_name}',
                        'he' => 'ביצוע חדש שנוצר על-ידי {user_name}',
                        'it' => 'Nuovo lead creato da {user_name}',
                        'ja' => '{user_name} によって作成された新しいリード',
                        'nl' => 'Nieuwe lead gemaakt door {user_name}',
                        'pl' => 'Nowy potencjalny klient utworzony przez użytkownika {user_name}',
                        'ru' => 'Новый интерес создан пользователем {user_name}',
                        'pt' => 'Novo lead criado por {user_name}',
                        'tr' => '{ user_name } tarafından oluşturulan Yeni Lider',
                        'pt-br' => 'Novo Lead criado por {user_name}',
                    ]
                ],
                'lead_to_deal_conversion' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Lead User Name": "lead_user_name",
                    "Lead Name": "lead_name",
                    "Lead Email": "lead_email"
                    }',
                    'lang' => [
                        'ar' => 'تم تحويل الصفقة من خلال العميل المحتمل {lead_user_name}',
                        'zh' =>'已通过商机 {lead_user_name} 进行转换',
                        'da' => 'Aftale konverteret via kundeemne {lead_user_name}',
                        'de' => 'Geschäftsabschluss durch Lead {lead_user_name}',
                        'en' => 'Deal converted through lead {lead_user_name}',
                        'es' => 'Trato convertido a través del cliente potencial {lead_user_name}',
                        'fr' => 'Offre convertie via le prospect {lead_user_name}',
                        'he' => 'העסקה הומרה באמצעות עופרת {lead_user_name}',
                        'it' => 'Offerta convertita tramite il lead {lead_user_name}',
                        'ja' => 'リード {lead_user_name} を通じて商談が成立',
                        'nl' => 'Deal geconverteerd via lead {lead_user_name}',
                        'pl' => 'Umowa przekonwertowana przez lead {lead_user_name}',
                        'ru' => 'Конвертация сделки через лид {lead_user_name}',
                        'pt' => 'Negócio convertido por meio do lead {lead_user_name}',
                        'tr' => 'Baş { lead_user_name } ile dönüştürülen anlaşma',
                        'pt-br' => 'Acordo convertido através do lead {lead_user_name}',
                    ]
                ],
                'new_project' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Project Name": "project_name"
                    }',
                    'lang' => [
                        'ar' => 'تم تكوين مشروع جديد { project_name } بواسطة { user_name }',
                        'zh' => '{user_name} 创建了新的 {project_name} 项目',
                        'da' => 'Nyt { project_name } projekt oprettet af { user_name }',
                        'de' => 'Neues Projekt {project_name} erstellt von {user_name}',
                        'en' => 'New {project_name} project created by {user_name}.',
                        'es' => 'Nuevo proyecto {project_name} creado por {user_name}',
                        'fr' => 'Nouveau projet { project_name } créé par { nom_utilisateur }',
                        'he' => 'פרויקט {project_name} חדש שנוצר על ידי {user_name}',
                        'it' => 'Nuovo progetto {project_name} creato da {user_name}',
                        'ja' => '{user_name} によって作成された新規 {project_name} プロジェクト',
                        'nl' => 'Nieuw project { project_name } gemaakt door { user_name }',
                        'pl' => 'Nowy projekt {project_name } utworzony przez użytkownika {user_name }',
                        'ru' => 'Новый проект { project_name }, созданный пользователем { user_name }',
                        'pt' => 'Novo projeto {project_name} criado por {user_name}',
                        'tr' => '{ user_name } tarafından oluşturulan yeni { project_name } projesi',
                        'pt-br' => 'Novo projeto {project_name} criado por {user_name}',
                    ]
                ],
                'task_stage_updated' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Task Name": "task_name",
                    "Old Stage Name": "old_stage_name",
                    "New Stage Name": "new_stage_name"
                    }',
                    'lang' => [
                        'ar' => 'تم تغيير حالة { task_name } من { old_stage_name } الى { new_stage_name }',
                        'zh' => '{task_name} 状态已从 {old_stage_name} 更改为 {new_stage_name}',
                        'da' => 'Status for { task_name } er ændret fra { old_stage_name } til { new_stage_name }',
                        'de' => 'Status {task_name} wurde von {old_stage_name} in {new_stage_name} geändert',
                        'en' => '{task_name} status changed from {old_stage_name} to {new_stage_name}',
                        'es' => 'El estado de {task_name} cambió de {old_stage_name} a {new_stage_name}',
                        'fr' => 'Le statut de {task_name} est passé de {old_stage_name} à {new_stage_name}',
                        'he' => 'הסטאטוס {task_name} השתנה מ - {old_stage_name} ל - {new_stage_name}',
                        'it' => 'Lo stato di {task_name} è cambiato da {old_stage_name} a {new_stage_name}',
                        'ja' => '{task_name} のステータスが {old_stage_name} から {new_stage_name} に変更されました',
                        'nl' => '{task_name}-status gewijzigd van {old_stage_name} in {new_stage_name}',
                        'pl' => 'Zmieniono status {task_name} z {old_stage_name} na {new_stage_name}',
                        'ru' => 'Статус {task_name} изменен с {old_stage_name} на {new_stage_name}',
                        'pt' => '{task_name} status alterado de {old_stage_name} para {new_stage_name}',
                        'tr' => '{ task_name } durumu, { old_stage_name } tarafından { new_stage_name } olarak değiştirildi',
                        'pt-br' => '{task_name} status alterado de {old_stage_name} para {new_stage_name}',
                    ]
                ],
                'new_deal' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Lead Name": "deal_name"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء الصفقة الجديدة بواسطة {user_name}',
                        'zh' => '{user_name} 创建的新政',
                        'da' => 'Ny aftale oprettet af {user_name}',
                        'de' => 'Neuer Deal erstellt von {user_name}',
                        'en' => 'New Deal created by {user_name}',
                        'es' => 'Nueva oferta creada por {user_name}',
                        'fr' => 'Nouvelle offre créée par {user_name}',
                        'he' => 'עסקה חדשה שנוצרה על-ידי {user_name}',
                        'it' => 'New Deal creato da {user_name}',
                        'ja' => '{user_name} によって作成された新しいディール',
                        'nl' => 'Nieuwe deal gemaakt door {user_name}',
                        'pl' => 'Nowa oferta utworzona przez użytkownika {user_name}',
                        'ru' => 'Новая сделка создана пользователем {user_name}',
                        'pt' => 'Novo negócio criado por {user_name}',
                        'tr' => '{ user_name } tarafından oluşturulan Yeni Anlaşma',
                        'pt-br' => 'Novo negócio criado por {user_name}',
                    ]
                ],
                'new_contract' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Contract Name": "contract_subject",
                    "Client Name": "contract_client",
                    "Contract Price": "contract_value",
                    "Contract Start Date": "contract_start_date",
                    "Contract End Date": "contract_end_date"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء عقد {Contract_subject} لـ {contract_client} بواسطة {user_name}',
                        'zh' => '{contract_subject } 合同已由 {user_name} 创建 { contract_client}',
                        'da' => '{contract_subject} kontrakt oprettet for {contract_client} af {user_name}',
                        'de' => '{contract_subject} Vertrag erstellt für {contract_client} von {user_name}',
                        'en' => '{contract_subject} contract created for {contract_client} by {user_name}',
                        'es' => '{contract_subject} contrato creado para {contract_client} por {user_name}',
                        'fr' => 'Contrat {contract_subject} créé pour {contract_client} par {user_name}',
                        'he' => '{contract_subject} חוזה שנוצר עבור {contract_client} על-ידי {user_name}',
                        'it' => 'Contratto {contract_subject} creato per {contract_client} da {user_name}',
                        'ja' => '{user_name} によって {contract_client} のために作成された {contract_subject} 契約',
                        'nl' => '{contract_subject} contract gemaakt voor {contract_client} door {user_name}',
                        'pl' => 'Umowa {contract_subject} utworzona dla {contract_client} przez {user_name}',
                        'ru' => 'Контракт {contract_subject} создан для {contract_client} пользователем {user_name}',
                        'pt' => 'Contrato {contract_subject} criado para {contract_client} por {user_name}',
                        'tr' => '{ user_name } tarafından { contract_client } için { contract_subject } sözleşmesi oluşturuldu',
                        'pt-br' => 'Contrato {contract_subject} criado para {contract_client} por {user_name}',
                    ]
                ],
                'new_task' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Task Name": "task_name",
                    "Project Name": "project_name"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء مهمة {task_name} لمشروع {project_name} بواسطة {user_name}',
                        'zh' => '{user_name} 为 {project_name} 项目创建 {task_name} 任务',
                        'da' => '{task_name} opgave oprettet for {project_name}-projekt af {user_name}',
                        'de' => 'Aufgabe {task_name} erstellt für Projekt {project_name} von {user_name}',
                        'en' => '{task_name} task create for {project_name} project by {user_name}.',
                        'es' => '{task_name} tarea creada para {project_name} proyecto por {user_name}',
                        'fr' => 'Tâche {task_name} créée pour le projet {project_name} par {user_name}',
                        'he' => 'המשימה {task_name} יוצרת עבור {project_name} פרויקט על ידי {user_name}',
                        'it' => 'Attività {task_name} creata per il progetto {project_name} da {user_name}',
                        'ja' => '{user_name} による {project_name} プロジェクトの {task_name} タスク作成',
                        'nl' => '{task_name} taak gemaakt voor {project_name} project door {user_name}',
                        'pl' => 'Zadanie {task_name} utworzono dla projektu {project_name} przez użytkownika {user_name}',
                        'ru' => 'Задача {task_name} создана для проекта {project_name} пользователем {user_name}',
                        'pt' => 'Tarefa {task_name} criada para o projeto {project_name} por {user_name}',
                        'tr' => '{ user_name } tarafından { proje_name } projesi için { task_name } görev oluşturma',
                        'pt-br' => 'Tarefa {task_name} criada para o projeto {project_name} por {user_name}',
                    ]
                ],
                'new_task_comment' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Task Name": "task_name",
                    "Project Name": "project_name"
                    }',
                    'lang' => [
                        'ar' => 'تمت إضافة تعليق جديد في المهمة {task_name} للمشروع {project_name}',
                        'zh' => '项目 {project_name} 的任务 {task_name} 中添加了新注释',
                        'da' => 'Ny kommentar tilføjet til opgave {task_name} i projekt {project_name}',
                        'de' => 'Neuer Kommentar in Aufgabe {task_name} von Projekt {project_name} hinzugefügt',
                        'en' => 'New Comment added in task {task_name} of project {project_name}.',
                        'es' => 'Nuevo comentario agregado en la tarea {task_name} del proyecto {project_name}',
                        'fr' => 'Nouveau commentaire ajouté dans la tâche {task_name} du projet {project_name}',
                        'he' => 'הערה חדשה נוספה במשימה {task_name} של הפרויקט {project_name}',
                        'it' => "Nuovo commento aggiunto nell'attività {task_name} del progetto {project_name}",
                        'ja' => 'プロジェクト {project_name} のタスク {task_name} に新しいコメントが追加されました',
                        'nl' => 'Nieuwe opmerking toegevoegd in taak {task_name} van project {project_name}',
                        'pl' => 'Dodano nowy komentarz w zadaniu {task_name} projektu {project_name}',
                        'ru' => 'Новый комментарий добавлен в задачу {task_name} проекта {project_name}',
                        'pt' => 'Novo comentário adicionado na tarefa {task_name} do projeto {project_name}',
                        'tr' => '{ project_name } projesinin { task_name } görevine yeni bir yorum eklendi',
                        'pt-br' => 'Novo comentário adicionado na tarefa {task_name} do projeto {project_name}',
                    ]
                ],
                'new_monthly_payslip' => [
                    'variables' => '{
                    "Year": "year"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء قسيمة دفع جديدة بتاريخ {year}',
                        'zh' => '{ y年内} 生成的新 payslip',
                        'da' => 'Ny lønseddel genereret af {year}',
                        'de' => 'Neue Gehaltsabrechnung erstellt vom {year}',
                        'en' => 'New payslip generated of {year}',
                        'es' => 'Nueva nómina generada de {year}',
                        'fr' => 'Nouvelle fiche de paie générée de {year}',
                        'he' => 'תשלום חדש שהופק מ - {year}',
                        'it' => 'Nuova busta paga generata di {year}',
                        'ja' => '{year} の新しい給​​与明細が作成されました',
                        'nl' => 'Nieuwe loonstrook gegenereerd van {year}',
                        'pl' => 'Nowy odcinek wypłaty wygenerowany za {year}',
                        'ru' => 'Новая расчетная ведомость создана за {year}',
                        'pt' => 'Novo contracheque gerado de {year}',
                        'tr' => '{ year } tarafından oluşturulan yeni payslip',
                        'pt-br' => 'Novo contracheque gerado de {year}',
                    ]
                ],
                'new_announcement' => [
                    'variables' => '{
                    "Announcement Title": "announcement_title",
                    "Branch Name": "branch_name",
                    "Start Date": "start_date",
                    "End Date": "end_date"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء إعلان {calling_title} للفرع {Branch_name} من {start_date} إلى {end_date}',
                        'zh' => '已为分支 {branch_name} 从 {start_date} 到 {end_date} 创建 {announcement_title} 声明',
                        'da' => '{announcement_title}-meddelelse oprettet for filial {branch_name} fra {start_date} til {end_date}',
                        'de' => '{announcement_title}-Ankündigung erstellt für Branche {branch_name} von {start_date} bis {end_date}',
                        'en' => '{announcement_title} announcement created for branch {branch_name} from {start_date} to {end_date}.',
                        'es' => 'Anuncio {announcement_title} creado para la sucursal {branch_name} desde el {start_date} hasta el {end_date}',
                        'fr' => '{announcement_title} annonce créée pour la succursale {branch_name} du {start_date} au {end_date}',
                        'he' => '{להכריז על הכרזה שנוצרה עבור ענף {מיתוג} מ - {start_date} ל - {end_date}',
                        'it' => '{announcement_title} annuncio creato per la filiale {branch_name} dal {start_date} al {end_date}',
                        'ja' => '{announcement_title} ブランチ {branch_name} の {start_date} から {end_date} までのお知らせが作成されました',
                        'nl' => '{announcement_title} aankondiging gemaakt voor filiaal {branch_name} van {start_date} tot {end_date}',
                        'pl' => 'Ogłoszenie {announcement_title} utworzone dla oddziału {branch_name} od {start_date} do {end_date}',
                        'ru' => 'Объявление {announcement_title} создано для филиала {branch_name} с {start_date} по {end_date}',
                        'pt' => 'Anúncio de {announcement_title} criado para a filial {branch_name} de {start_date} a {end_date}',
                        'tr' => '{ branch_name } dalı için { start_date }-{ end_date } tarihleri arasında { announcement_title } duyurusu oluşturuldu',
                        'pt-br' => 'Anúncio de {announcement_title} criado para a filial {branch_name} de {start_date} a {end_date}',
                    ]
                ],
                'new_support_ticket' => [
                    'variables' => '{
                    "Support Priority": "support_priority",
                    "Support User Name": "support_user_name"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء بطاقة دعم جديدة ذات أولوية {support_priority} لـ {support_user_name}',
                        'zh' => '为 {support_user_name} 创建了 { support_priority} 优先级的新支持凭单',
                        'da' => 'Ny supportbillet oprettet med prioritet {support_priority} til {support_user_name}',
                        'de' => 'Neues Support-Ticket mit Priorität {support_priority} für {support_user_name} erstellt',
                        'en' => 'New Support ticket created of {support_priority} priority for {support_user_name}',
                        'es' => 'Nuevo ticket de soporte creado con prioridad {support_priority} para {support_user_name}',
                        'fr' => "Nouveau ticket d'assistance créé avec la priorité {support_priority} pour {support_user_name}",
                        'he' => "כרטיס תמיכה חדש שנוצר עבור קדימות {support_priority} עבור {support_user_name}",
                        'it' => 'Nuovo ticket di assistenza creato con priorità {support_priority} per {support_user_name}',
                        'ja' => '{support_user_name} の優先度 {support_priority} の新しいサポート チケットが作成されました',
                        'nl' => 'Nieuw ondersteuningsticket gemaakt met prioriteit {support_priority} voor {support_user_name}',
                        'pl' => 'Utworzono nowe zgłoszenie do pomocy technicznej o priorytecie {support_priority} dla użytkownika {support_user_name}',
                        'ru' => 'Создан новый запрос в службу поддержки с приоритетом {support_priority} для {support_user_name}',
                        'pt' => 'Novo tíquete de suporte criado com prioridade {support_priority} para {support_user_name}',
                        'tr' => '{ support_user_name } için { support_priority } önceliğine ilişkin yeni Destek bileti oluşturuldu',
                        'pt-br' => 'Novo tíquete de suporte criado com prioridade {support_priority} para {support_user_name}',
                    ]
                ],
                'new_meeting' => [
                    'variables' => '{
                    "Meeting Title": "meeting_title",
                    "Branch Name": "branch_name",
                    "Meeting Date": "meeting_date",
                    "Meeting Time": "meeting_time"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء اجتماع {meeting_title} للفرع {Branch_name} من {meeting_date} في {meeting_time}',
                        'zh' => '已从 { meeting_time} 为分支 {branch_name} 创建了 { meetting_title } 会议 { meeting_date}',
                        'da' => '{meeting_title} møde oprettet for filial {branch_name} fra {meeting_date} kl. {meeting_time}',
                        'de' => '{meeting_title}-Meeting für Zweigstelle {branch_name} vom {meeting_date} um {meeting_time} erstellt',
                        'en' => '{meeting_title} meeting created for branch {branch_name} from {meeting_date} at {meeting_time}.',
                        'es' => '{meeting_title} reunión creada para la sucursal {branch_name} de {meeting_date} a las {meeting_time}',
                        'fr' => 'Réunion {meeting_title} créée pour la succursale {branch_name} à partir du {meeting_date} à {meeting_time}',
                        'he' => '{meeting_title} פגישה שנוצרה עבור ענף {מיתוג} מתוך {meeting_date} ב - {meeting_time}',
                        'it' => 'Meeting {meeting_title} creato per la filiale {branch_name} da {meeting_date} alle {meeting_time}',
                        'ja' => '{meeting_date} から {meeting_time} に {meeting_title} ブランチ {branch_name} 用に作成された {meeting_title} ミーティング',
                        'nl' => '{meeting_title} vergadering gemaakt voor filiaal {branch_name} vanaf {meeting_date} om {meeting_time}',
                        'pl' => 'Spotkanie {meeting_title} utworzone dla oddziału {branch_name} od {meeting_date} o {meeting_time}',
                        'ru' => 'Встреча {meeting_title} создана для филиала {branch_name} с {meeting_date} в {meeting_time}',
                        'pt' => 'Reunião {meeting_title} criada para a filial {branch_name} de {meeting_date} às {meeting_time}',
                        'tr' => '{ meeting_title } { branch_name } dalı için { meeting_date } dalından { meeting_time } saatinde oluşturulan toplantı oluşturuldu',
                        'pt-br' => 'Reunião {meeting_title} criada para a filial {branch_name} de {meeting_date} às {meeting_time}',
                    ]
                ],
                'new_award' => [
                    'variables' => '{
                    "Award Name": "award_name",
                    "Employee Name": "employee_name",
                    "Award Date": "award_date"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء {Award_name} لـ {Employee_name} من {Award_date}',
                        'zh' => '已从 {award_date} 为 {employe_name} 创建 {award_name}',
                        'da' => '{award_name} oprettet til {employee_name} fra {award_date}',
                        'de' => '{award_name} erstellt für {employee_name} vom {award_date}',
                        'en' => '{award_name} created for {employee_name} from {award_date}',
                        'es' => '{award_name} creado para {employee_name} de {award_date}',
                        'fr' => '{award_name} créé pour {employee_name} à partir du {award_date}',
                        'he' => '{award_name} שנוצר עבור {העובד ee_name} מ - {award_date}',
                        'it' => '{award_name} creato per {employee_name} da {award_date}',
                        'ja' => '{employee_name} のために {award_name} が {award_date} から作成されました',
                        'nl' => '{award_name} gemaakt voor {employee_name} vanaf {award_date}',
                        'pl' => '{award_name} utworzone dla {employee_name} od {award_date}',
                        'ru' => '{award_name} создано для {employee_name} с {award_date}',
                        'pt' => '{award_name} criado para {employee_name} de {award_date}',
                        'tr' => '{ employee_name } için { award_date } içinden { award_name } oluşturuldu',
                        'pt-br' => '{award_name} criado para {employee_name} de {award_date}',
                    ]
                ],
                'new_holiday' => [
                    'variables' => '{
                    "Holiday Title": "holiday_title",
                    "Holiday Date": "holiday_date"
                    }',
                    'lang' => [
                        'ar' => '{holiday_title} عطلة يوم {holiday_date}',
                        'zh' => '{holiday_date} 上的 {holiday_title} 假日',
                        'da' => '{holiday_title} helligdag på {holiday_date}',
                        'de' => '{holiday_title} Feiertag am {holiday_date}',
                        'en' => '{holiday_title} holiday on {holiday_date}',
                        'es' => '{holiday_title} feriado el {holiday_date}',
                        'fr' => '{holiday_title} vacances le {holiday_date}',
                        'he' => '{הולידיי _title} חגים ב - {הולידיי _date}',
                        'it' => '{holiday_title} festività il giorno {holiday_date}',
                        'ja' => '{holiday_date} の {holiday_title} 休日',
                        'nl' => '{holiday_title} vakantie op {holiday_date}',
                        'pl' => '{holiday_title} wakacje w dniu {holiday_date}',
                        'ru' => '{holiday_title} праздник {holiday_date}',
                        'pt' => '{holiday_title} feriado em {holiday_date}',
                        'tr' => '{ holiday_date } tarihinde ({ holiday_date })',
                        'pt-br' => '{holiday_title} feriado em {holiday_date}',
                    ]
                ],
                'new_event' => [
                    'variables' => '{
                    "Event Title": "event_title",
                    "Branch Name": "branch_name",
                    "Event Start Date": "event_start_date",
                    "Event End Date": "event_end_date"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء حدث {event_title} للفرع {Branch_name} من {event_start_date} إلى {event_end_date}',
                        'zh' => '为分支 {branch_name} 从 {event_start_date} 创建的 {event_title } 事件为 {event_end_date}',
                        'da' => '{event_title}-begivenhed oprettet for grenen {branch_name} fra {event_start_date} til {event_end_date}',
                        'de' => '{event_title} Veranstaltung erstellt für Branche {branch_name} von {event_start_date} bis {event_end_date}',
                        'en' => '{event_title} event created for branch {branch_name} from {event_start_date} to {event_end_date}',
                        'es' => '{event_title} evento creado para la sucursal {branch_name} desde el {event_start_date} hasta el {event_end_date}',
                        'fr' => 'Événement {event_title} créé pour la succursale {branch_name} du {event_start_date} au {event_end_date}',
                        'he' => '{event_title} אירוע שנוצר עבור ענף {ברנch_name} מ - {event_start_date} אל {event_end_date}',
                        'it' => 'Evento {event_title} creato per il ramo {branch_name} da {event_start_date} a {event_end_date}',
                        'ja' => '{event_title} ブランチ {branch_name} に対して {event_start_date} から {event_end_date} まで作成された {event_title} イベント',
                        'nl' => '{event_title} evenement gemaakt voor filiaal {branch_name} van {event_start_date} tot {event_end_date}',
                        'pl' => 'Wydarzenie {event_title} utworzone dla oddziału {branch_name} od {event_start_date} do {event_end_date}',
                        'ru' => 'Событие {event_title} создано для филиала {branch_name} с {event_start_date} по {event_end_date}',
                        'pt' => 'Evento {event_title} criado para a ramificação {branch_name} de {event_start_date} a {event_end_date}',
                        'tr' => '{ branch_name } dalı için { event_start_date }-{ event_end_date } tarihleri arasında { event_title } olayı yaratıldı',
                        'pt-br' => 'Evento {event_title} criado para a ramificação {branch_name} de {event_start_date} a {event_end_date}',
                    ]
                ],
                'new_company_policy' => [
                    'variables' => '{
                    "Company Policy Name": "company_policy_name",
                    "Branch Name": "branch_name"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء سياسة {company_policy_name} لفرع {Branch_name}',
                        'zh' => '已创建 {branch_name} 分支的 {company_policy_name} 策略',
                        'da' => '{company_policy_name}-politik for filialen {branch_name} er oprettet',
                        'de' => 'Richtlinie {company_policy_name} für Zweigstelle {branch_name} erstellt',
                        'en' => '{company_policy_name} policy for {branch_name} branch created',
                        'es' => 'Política {company_policy_name} para la sucursal {branch_name} creada',
                        'fr' => 'Stratégie {company_policy_name} pour la succursale {branch_name} créée',
                        'he' => '{company_policy_name} מדיניות עבור ענף {מיתוג} נוצרה',
                        'it' => 'Politica {company_policy_name} per la filiale {branch_name} creata',
                        'ja' => '{branch_name} ブランチの {company_policy_name} ポリシーが作成されました',
                        'nl' => '{company_policy_name}-beleid voor filiaal {branch_name} gemaakt',
                        'pl' => 'Polityka {company_policy_name} dla oddziału {branch_name} została utworzona',
                        'ru' => 'Создана политика {company_policy_name} для филиала {branch_name}',
                        'pt' => 'política {company_policy_name} para a filial {branch_name} criada',
                        'tr' => '{ branch_name } şubesi için { company_policy_name } ilkesi oluşturuldu',
                        'pt-br' => 'política {company_policy_name} para a filial {branch_name} criada',
                    ]
                ],
                'new_invoice' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Invoice Number": "invoice_number",
                    "Invoice Issue Date": "invoice_issue_date",
                    "Invoice Due Date": "invoice_due_date",
                    "Customer Name": "customer_name"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء الفاتورة الجديدة {invoice_number} بواسطة {user_name}',
                        'zh' => '{user_name} 创建的新发票 {invoice_number}',
                        'da' => 'Ny faktura {invoice_number} oprettet af {user_name}',
                        'de' => 'Neue Rechnung {invoice_number} erstellt von {user_name}',
                        'en' => 'New Invoice {invoice_number} created by {user_name}.',
                        'es' => 'Nueva factura {invoice_number} creada por {user_name}',
                        'fr' => 'Nouvelle facture {invoice_number} créée par {user_name}',
                        'he' => 'חשבונית חדשה {invoice_number} נוצרה על-ידי {user_name}',
                        'it' => 'Nuova fattura {invoice_number} creata da {user_name}',
                        'ja' => '{user_name} によって作成された新しい請求書 {invoice_number}',
                        'nl' => 'Nieuwe factuur {invoice_number} gemaakt door {user_name}',
                        'pl' => 'Nowa faktura {invoice_number} utworzona przez użytkownika {user_name}',
                        'ru' => 'Новый счет {invoice_number}, созданный {user_name}',
                        'pt' => 'Nova fatura {invoice_number} criada por {user_name}',
                        'tr' => 'Yeni Fatura { invoice_number }, { user_name } tarafından oluşturuldu',
                        'pt-br' => 'Nova fatura {invoice_number} criada por {user_name}',
                    ]
                ],
                'new_bill' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Bill Number": "bill_number",
                    "Bill Date": "bill_date",
                    "Bill Due Date": "bill_due_date",
                    "Vendor Name": "vendor_name"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء الفاتورة الجديدة {bill_number} بواسطة {user_name}',
                        'zh' => '{ user_name} 创建的新帐单 {bill_number}',
                        'da' => 'Ny regning {bill_number} oprettet af {user_name}',
                        'de' => 'Neue Rechnung {bill_number} erstellt von {user_name}',
                        'en' => 'New Bill {bill_number} created by {user_name}',
                        'es' => 'Nueva factura {bill_number} creada por {user_name}',
                        'fr' => 'Nouvelle facture {bill_number} créée par {user_name}',
                        'he' => '{ user_name} 创建的新帐单 {bill_number}',
                        'it' => 'Nuova fattura {bill_number} creata da {user_name}',
                        'ja' => '{user_name} によって作成された新しい請求書 {bill_number}',
                        'nl' => 'Nieuwe factuur {bill_number} gemaakt door {user_name}',
                        'pl' => 'Nowy rachunek {bill_number} utworzony przez użytkownika {user_name}',
                        'ru' => 'Новый счет {bill_number}, созданный {user_name}',
                        'pt' => 'Nova fatura {bill_number} criada por {user_name}',
                        'tr' => '{ user_name } tarafından oluşturulan yeni Fatura { bill_number }',
                        'pt-br' => 'Nova fatura {bill_number} criada por {user_name}',
                    ]
                ],
                'new_budget' => [
                    'variables' => '{
                    "Budget Period": "budget_period",
                    "Budget Year": "budget_year",
                    "Budget Name": "budget_name"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء ميزانية {budget_period} البالغة {budget_year} لـ {budget_name}',
                        'zh' => '已为 {budget_name} 创建 {budget_period} 预算 { budget_period }',
                        'da' => '{budget_period} budget på {budget_year} oprettet for {budget_name}',
                        'de' => '{budget_period} Budget von {budget_year} erstellt für {budget_name}',
                        'en' => '{budget_period} budget of {budget_year} created for {budget_name}',
                        'es' => '{budget_period} presupuesto de {budget_year} creado para {budget_name}',
                        'fr' => '{budget_period} budget de {budget_year} créé pour {budget_name}',
                        'he' => '{budget_לתקופת} תקציב של {budget_year} שנוצר עבור {budget_name}',
                        'it' => '{budget_period} budget di {budget_year} creato per {budget_name}',
                        'ja' => '{budget_name} 用に作成された {budget_year} の {budget_period} 予算',
                        'nl' => '{budget_period} budget van {budget_year} gemaakt voor {budget_name}',
                        'pl' => 'Budżet {budget_period} w wysokości {budget_year} został utworzony dla {budget_name}',
                        'ru' => 'Бюджет {budget_period} на {budget_year} создан для {budget_name}',
                        'pt' => 'Orçamento de {budget_period} de {budget_year} criado para {budget_name}',
                        'tr' => '{ budget_year }, { budget_name } için { budget_period } bütçesi oluşturuldu',
                        'pt-br' => 'Orçamento de {budget_period} de {budget_year} criado para {budget_name}',
                    ]
                ],
                'new_revenue' => [
                    'variables' => '{
                    "Company Name": "user_name",
                    "Revenue Amount": "revenue_amount",
                    "Customer Name": "customer_name",
                    "Revenue Date": "revenue_date"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء الإيرادات الجديدة من {الأرباح_amount} لـ {customer_name} بواسطة {user_name}',
                        'zh' => '{user_name} 为 {customer_name} 创建的新收入 { 金额 }',
                        'da' => 'Ny omsætning på {revenue_amount} oprettet for {customer_name} af {user_name}',
                        'de' => 'Neuer Umsatz von {revenue_amount} erstellt für {customer_name} von {user_name}',
                        'en' => 'New Revenue of {revenue_amount} created for {customer_name} by {user_name}',
                        'es' => 'Nuevos ingresos de {revenue_amount} creados para {customer_name} por {user_name}',
                        'fr' => 'Nouveau revenu de {revenue_amount} créé pour {customer_name} par {user_name}',
                        'he' => 'הכנסה חדשה של {Revenue_סכום} שנוצרה עבור {customer_name} על-ידי {user_name}',
                        'it' => 'Nuove entrate di {revenue_amount} create per {customer_name} da {user_name}',
                        'ja' => '{user_name} によって {customer_name} に作成された {revenue_amount} の新しい収入',
                        'nl' => 'Nieuwe opbrengst van {revenue_amount} gecreëerd voor {customer_name} door {user_name}',
                        'pl' => 'Nowy przychód w wysokości {revenue_amount} utworzony dla klienta {customer_name} przez użytkownika {user_name}',
                        'ru' => 'Новый доход в размере {revenue_amount} создан для {customer_name} пользователем {user_name}',
                        'pt' => 'Nova receita de {revenue_amount} criada para {customer_name} por {user_name}',
                        'tr' => '{ user_name } tarafından { customer_name } için yeni { revenue_amount } Geliri oluşturuldu',
                        'pt-br' => 'Nova receita de {revenue_amount} criada para {customer_name} por {user_name}',
                    ]
                ],
                'new_invoice_payment' => [
                    'variables' => '{
                    "Payment Price": "payment_price",
                    "Customer Name": "customer_name",
                    "Payment Type": "invoice_payment_type"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء دفعة جديدة بقيمة {payment_price} لـ {customer_name} بواسطة {invoice_payment_type}',
                        'zh' => '{invoice_payment_type} 为 {customer_name} 创建了新支付 { payment_price}',
                        'da' => 'Ny betaling på {payment_price} oprettet for {customer_name} af {invoice_payment_type}',
                        'de' => 'Neue Zahlung von {payment_price} erstellt für {customer_name} von {invoice_payment_type}',
                        'en' => 'New payment of {payment_price} created for {customer_name} by {invoice_payment_type}',
                        'es' => 'Nuevo pago de {payment_price} creado para {customer_name} por {invoice_payment_type}',
                        'fr' => 'Nouveau paiement de {payment_price} créé pour {customer_name} par {invoice_payment_type}',
                        'he' => 'תשלום חדש של {payment_פרייס} שנוצר עבור {customer_name} על-ידי {invoice_payment_type}',
                        'it' => 'Nuovo pagamento di {payment_price} creato per {customer_name} da {invoice_payment_type}',
                        'ja' => '{invoice_payment_type} によって {customer_name} のために作成された {payment_price} の新しい支払い',
                        'nl' => 'Nieuwe betaling van {payment_price} gemaakt voor {customer_name} door {invoice_payment_type}',
                        'pl' => 'Nowa płatność {payment_price} utworzona dla {customer_name} przez {invoice_payment_type}',
                        'ru' => 'Создан новый платеж {payment_price} для {customer_name} по {invoice_payment_type}',
                        'pt' => 'Novo pagamento de {payment_price} criado para {customer_name} por {invoice_payment_type}',
                        'tr' => '{ customer_name } için { invoice_payment_type } tarafından oluşturulan { payment_price } için yeni ödeme',
                        'pt-br' => 'Novo pagamento de {payment_price} criado para {customer_name} por {invoice_payment_type}',
                    ]
                ],
                'new_customer' => [
                    'variables' => '{
                    "Customer Name": "customer_name",
                    "Customer Email": "customer_email"
                    }',
                    'lang' => [
                        'ar' => 'عميل جديد أنشأه {user_name}',
                        'zh' => '由 {user_name} 创建的新客户',
                        'da' => 'Ny kunde oprettet af {user_name}',
                        'de' => 'Neuer Kunde erstellt von {user_name}',
                        'en' => 'New Customer created by {user_name}',
                        'es' => 'Nuevo cliente creado por {user_name}',
                        'fr' => 'Nouveau client créé par {user_name}',
                        'he' => 'לקוח חדש נוצר על-ידי {user_name}',
                        'it' => 'Nuovo cliente creato da {user_name}',
                        'ja' => '{user_name} によって作成された新しい顧客',
                        'nl' => 'Nieuwe klant gemaakt door {user_name}',
                        'pl' => 'Nowy klient utworzony przez {user_name}',
                        'ru' => 'Новый клиент создан {user_name}',
                        'pt' => 'Novo cliente criado por {user_name}',
                        'tr' => '{ user_name } tarafından oluşturulan yeni Müşteri',
                        'pt-br' => 'Novo cliente criado por {user_name}',
                    ]
                ],
                'new_vendor' => [
                    'variables' => '{
                    "Vendor Name": "vendor_name",
                    "Vendor Email": "vendor_email"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء بائع جديد بواسطة {user_name}',
                        'zh' => '{user_name} 创建的新供应商',
                        'da' => 'Ny leverandør oprettet af {user_name}',
                        'de' => 'Neuer Anbieter erstellt von {user_name}',
                        'en' => 'New Vendor created by {user_name}',
                        'es' => 'Nuevo proveedor creado por {user_name}',
                        'fr' => 'Nouveau fournisseur créé par {user_name}',
                        'he' => 'משווק חדש שנוצר על-ידי {user_name}',
                        'it' => 'Nuovo fornitore creato da {user_name}',
                        'ja' => '{user_name} によって作成された新しいベンダー',
                        'nl' => 'Nieuwe leverancier gemaakt door {user_name}',
                        'pl' => 'Nowy dostawca utworzony przez {user_name}',
                        'ru' => 'Новый поставщик создан пользователем {user_name}',
                        'pt' => 'Novo fornecedor criado por {user_name}',
                        'tr' => '{ user_name } tarafından oluşturulan Yeni Satıcı',
                        'pt-br' => 'Novo fornecedor criado por {user_name}',
                    ]
                ],
                'new_proposal' => [
                    'variables' => '{
                    "Proposal Number": "proposal_number",
                    "Company Name": "user_name",
                    "Customer Name": "customer_name",
                    "Proposal Issue Date": "proposal_issue_date"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء اقتراح جديد بواسطة {user_name}',
                        'zh' => '{user_name} 创建的新建议',
                        'da' => 'Nyt forslag oprettet af {user_name}',
                        'de' => 'Neues Angebot erstellt von {user_name}',
                        'en' => 'New Proposal created by {user_name}',
                        'es' => 'Nueva propuesta creada por {user_name}',
                        'fr' => 'Nouvelle proposition créée par {user_name}',
                        'he' => 'הצעה חדשה שנוצרה על-ידי {user_name}',
                        'it' => 'Nuova proposta creata da {user_name}',
                        'ja' => '{user_name} によって作成された新しい提案',
                        'nl' => 'Nieuw voorstel gemaakt door {user_name}',
                        'pl' => 'Nowa propozycja utworzona przez użytkownika {user_name}',
                        'ru' => 'Новое предложение, созданное {user_name}',
                        'pt' => 'Nova proposta criada por {user_name}',
                        'tr' => '{ user_name } tarafından oluşturulan Yeni Teklif',
                        'pt-br' => 'Nova proposta criada por {user_name}',
                    ]
                ],
                'bill_payment' => [
                    'variables' => '{
                    "Payment Amount": "payment_amount",
                    "Vendor Name": "vendor_name",
                    "Payment Type": "payment_type"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء دفعة جديدة بقيمة {payment_amount} لـ {vendor_name} بواسطة {payment_type}',
                        'zh' => '{payment_type} 为 {vendor_name} 创建了新的支付 { payment_金额}',
                        'da' => 'Ny betaling på {payment_amount} oprettet for {vendor_name} af {payment_type}',
                        'de' => 'Neue Zahlung in Höhe von {payment_amount} erstellt für {vendor_name} von {payment_type}',
                        'en' => 'New payment of {payment_amount} created for {vendor_name} by {payment_type}',
                        'es' => 'Nuevo pago de {pago_cantidad} creado para {vendor_name} por {pago_tipo}',
                        'fr' => 'Nouveau paiement de {payment_amount} créé pour {vendor_name} par {payment_type}',
                        'he' => 'תשלום חדש של {payment_מאונט} שנוצר עבור {vendor_name} על-ידי {payment_type}',
                        'it' => 'Nuovo pagamento di {payment_amount} creato per {vendor_name} da {payment_type}',
                        'ja' => '{payment_type} によって {vendor_name} に対して作成された {payment_mount} の新しい支払い',
                        'nl' => 'Nieuwe betaling van {payment_amount} gemaakt voor {vendor_name} door {payment_type}',
                        'pl' => 'Nowa płatność {payment_amount} utworzona dla {vendor_name} przez {payment_type}',
                        'ru' => 'Создан новый платеж {payment_amount} для {vendor_name} по {payment_type}',
                        'pt' => 'Novo pagamento de {payment_amount} criado para {vendor_name} por {payment_type}',
                        'tr' => '{ payment_type } tarafından { vendor_name } için yeni { payment_amount } ödemesi oluşturuldu',
                        'pt-br' => 'Novo pagamento de {payment_amount} criado para {vendor_name} por {payment_type}',
                    ]
                ],
                'invoice_payment_reminder' => [
                    'variables' => '{
                    "Customer Name": "customer_name",
                    "Invoice Number": "invoice_number",
                    "Company Name": "user_name"
                    }',
                    'lang' => [
                        'ar' => 'تم إنشاء تذكير دفع جديد لـ {invoice_number} بواسطة {user_name}',
                        'zh' => '{ user_name} 创建的 { invoice_number} 的新支付提醒',
                        'da' => 'Ny betalingspåmindelse om {invoice_number} oprettet af {user_name}',
                        'de' => 'Neue Zahlungserinnerung von {invoice_number} erstellt von {user_name}',
                        'en' => 'New Payment Reminder of {invoice_number} created by {user_name}',
                        'es' => 'Nuevo recordatorio de pago de {invoice_number} creado por {user_name}',
                        'fr' => 'Nouveau rappel de paiement de {invoice_number} créé par {user_name}',
                        'he' => 'תזכורת חדשה לתשלום עבור {invoice_number} שנוצרה על-ידי {user_name}',
                        'it' => 'Nuovo sollecito di pagamento di {invoice_number} creato da {user_name}',
                        'ja' => '{user_name} によって作成された {invoice_number} の新しい支払い通知',
                        'nl' => 'Nieuwe betalingsherinnering van {invoice_number} gemaakt door {user_name}',
                        'pl' => 'Nowe przypomnienie o płatności {invoice_number} utworzone przez użytkownika {user_name}',
                        'ru' => 'Новое напоминание об оплате {invoice_number}, созданное {user_name}',
                        'pt' => 'Novo lembrete de pagamento de {invoice_number} criado por {user_name}',
                        'tr' => '{ user_name } tarafından oluşturulan { invoice_number } adlı yeni Ödeme Anımsatıcısı',
                        'pt-br' => 'Novo lembrete de pagamento de {invoice_number} criado por {user_name}',
                    ]
                ],


            ],
        ];

        foreach($notifications as $k => $n)
        {
            $ntfy = NotificationTemplates::where('slug',$k)->count();
            if($ntfy == 0)
            {
                $new = new NotificationTemplates();
                $new->name = $n;
                $new->slug = $k;
                $new->save();
            }else{
                $new = NotificationTemplates::where('slug',$k)->first();
            }

                foreach($defaultTemplate['notification'][$k]['lang'] as $lang => $content)
                {
                    $notification = NotificationTemplateLangs::where('parent_id', $new->id)->where('lang', $lang)->count();
                    if($notification==0){
                    NotificationTemplateLangs::create(
                        [
                            'parent_id' => $new->id,
                            'lang' => $lang,
                            'variables' => $defaultTemplate['notification'][$k]['variables'],
                            'content' => $content,

                            // supar admin get and set that id
                            'created_by' =>1,
                        ]
                    );

                }
            }
        }
    }
}
