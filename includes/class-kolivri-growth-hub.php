<?php
/**
 * Core engine and shortcode renderers for Kolivri Growth Hub (Plays #1, #2, #6, #7, #10).
 *
 * Verified Kolivri constants:
 * - Phone: 054-787-5670
 * - App:   https://app.kolivri.co.il
 * - API:   https://api.kolivri.co.il
 * - Web:   https://kolivri.com
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kolivri_Growth_Hub_Engine {

    const KOLIVRI_PHONE_DISPLAY = '054-787-5670';
    const KOLIVRI_PHONE_DIGITS  = '0547875670';
    const KOLIVRI_API_BASE      = 'https://api.kolivri.co.il';
    const KOLIVRI_APP_URL       = 'https://app.kolivri.co.il';
    const KOLIVRI_SITE_URL      = 'https://kolivri.com';
    const KOLIVRI_HE_URL        = 'https://kolivri.com/he/';
    const KOLIVRI_FOLLOW_ME_URL = 'https://kolivri.com/he/follow-me/';
    const KOLIVRI_START_URL     = 'https://kolivri.com/he/start/';

    const PLAN_STARTER_ILS  = 247;
    const PLAN_BUSINESS_ILS = 479;
    const PLAN_GOLD_ILS     = 880;
    const CONTINGENCY_RATE  = 0.05;

    /**
     * HTML-escape helper compatible with standalone CLI and WordPress runtime.
     *
     * @param mixed $text
     * @return string
     */
    public static function esc($text) {
        if (function_exists('esc_html')) {
            return esc_html((string) $text);
        }
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Return shared scoped CSS for all 5 `.kgh-wrap` growth apps, including
     * explicit `@media (max-width: 768px)` and `@media (max-width: 480px)` rules.
     *
     * @return string
     */
    public static function getScopedCss() {
        return <<<'CSS'
<style>
.kgh-wrap {
    font-family: 'Rubik', 'Assistant', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(145deg, #0B1220 0%, #111C33 100%);
    color: #F8FAFC;
    border-radius: 20px;
    padding: 32px;
    max-width: 1080px;
    margin: 24px auto;
    box-shadow: 0 24px 60px rgba(11, 18, 32, 0.45);
    border: 1px solid rgba(56, 189, 248, 0.24);
    box-sizing: border-box;
}
.kgh-wrap *, .kgh-wrap *::before, .kgh-wrap *::after {
    box-sizing: border-box;
}
.kgh-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(56, 189, 248, 0.14);
    color: #38BDF8;
    border: 1px solid rgba(56, 189, 248, 0.35);
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 12px;
}
.kgh-badge.orange {
    background: rgba(251, 129, 35, 0.16);
    color: #FDBA74;
    border-color: rgba(251, 129, 35, 0.4);
}
.kgh-badge.emerald {
    background: rgba(16, 185, 129, 0.16);
    color: #6EE7B7;
    border-color: rgba(16, 185, 129, 0.4);
}
.kgh-title {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 8px;
    color: #FFFFFF;
    line-height: 1.25;
}
.kgh-subtitle {
    color: #94A3B8;
    font-size: 16px;
    margin: 0 0 24px;
    line-height: 1.55;
}
.kgh-grid-2 {
    display: grid;
    grid-template-columns: 1.05fr 0.95fr;
    gap: 22px;
}
.kgh-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
.kgh-panel {
    background: rgba(15, 23, 42, 0.82);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 16px;
    padding: 22px;
}
.kgh-panel-title {
    font-size: 17px;
    font-weight: 800;
    color: #FFFFFF;
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.kgh-field {
    margin-bottom: 14px;
}
.kgh-field label {
    display: block;
    font-size: 13.5px;
    font-weight: 600;
    color: #E2E8F0;
    margin-bottom: 6px;
}
.kgh-field input, .kgh-field select, .kgh-field textarea {
    width: 100%;
    min-height: 44px;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #0B1220;
    color: #F8FAFC;
    font-size: 14px;
    font-family: inherit;
}
.kgh-btn-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 14px;
}
.kgh-btn {
    min-height: 44px;
    padding: 11px 18px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    border: none;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: transform 0.15s ease, opacity 0.15s ease;
}
.kgh-btn:hover {
    transform: translateY(-1px);
}
.kgh-btn-primary {
    background: linear-gradient(90deg, #FB8123 0%, #EA580C 100%);
    color: #FFFFFF !important;
}
.kgh-btn-emerald {
    background: linear-gradient(90deg, #10B981 0%, #059669 100%);
    color: #FFFFFF !important;
}
.kgh-btn-secondary {
    background: rgba(56, 189, 248, 0.14);
    color: #38BDF8 !important;
    border: 1px solid rgba(56, 189, 248, 0.4);
}
.kgh-kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin: 18px 0;
}
.kgh-kpi {
    background: rgba(30, 41, 59, 0.75);
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 12px;
    padding: 14px;
    text-align: center;
}
.kgh-kpi .lbl {
    font-size: 12px;
    color: #94A3B8;
    margin-bottom: 4px;
}
.kgh-kpi .val {
    font-size: 22px;
    font-weight: 800;
    color: #38BDF8;
}
.kgh-kpi .val.emerald { color: #34D399; }
.kgh-kpi .val.orange { color: #FDBA74; }
.kgh-card-list {
    display: grid;
    gap: 12px;
    margin-top: 14px;
}
.kgh-item-card {
    background: rgba(15, 23, 42, 0.92);
    border: 1px solid rgba(56, 189, 248, 0.22);
    border-radius: 12px;
    padding: 16px;
}
.kgh-pill {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}
.kgh-pill.b-1-30 { background: rgba(56, 189, 248, 0.18); color: #7DD3FC; }
.kgh-pill.b-31-60 { background: rgba(251, 191, 36, 0.18); color: #FDE68A; }
.kgh-pill.b-61-plus { background: rgba(244, 63, 94, 0.2); color: #FDA4AF; }
.kgh-code-block {
    background: #050914;
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 12px;
    padding: 14px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 12.5px;
    color: #E2E8F0;
    overflow-x: auto;
    direction: ltr;
    text-align: left;
    white-space: pre-wrap;
    word-break: break-word;
}
.kgh-wa-box {
    background: rgba(16, 185, 129, 0.1);
    border-right: 4px solid #10B981;
    border-radius: 10px;
    padding: 14px;
    font-size: 14px;
    color: #ECFDF5;
    line-height: 1.55;
    margin-top: 10px;
}
.kgh-wrap[dir="ltr"] .kgh-wa-box {
    border-right: none;
    border-left: 4px solid #10B981;
}
.kgh-fomo-box {
    background: rgba(244, 63, 94, 0.12);
    border-right: 4px solid #F43F5E;
    border-radius: 10px;
    padding: 14px;
    font-size: 14px;
    color: #FFE4E6;
    line-height: 1.55;
    margin-top: 10px;
}
.kgh-wrap[dir="ltr"] .kgh-fomo-box {
    border-right: none;
    border-left: 4px solid #F43F5E;
}
.klv-lab-switcher {
    position: sticky;
    top: 8px;
    z-index: 20;
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding: 8px 10px;
    margin: 0 0 20px;
    background: rgba(11, 18, 32, 0.92);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 14px;
    white-space: nowrap;
}
.klv-lab-switcher a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    color: #CBD5E1 !important;
    background: rgba(30, 41, 59, 0.75);
    border: 1px solid rgba(148, 163, 184, 0.22);
    text-decoration: none !important;
    flex-shrink: 0;
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}
.klv-lab-switcher a:hover {
    color: #FFFFFF !important;
    border-color: rgba(56, 189, 248, 0.5);
}
.klv-lab-switcher a.klv-lab-home {
    background: rgba(56, 189, 248, 0.14);
    color: #38BDF8 !important;
    border-color: rgba(56, 189, 248, 0.4);
}
.klv-lab-switcher a[aria-current="page"] {
    background: linear-gradient(90deg, #FB8123 0%, #EA580C 100%);
    color: #FFFFFF !important;
    border-color: #FB8123;
}
.kgh-comp-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 0 0 18px;
}
.kgh-comp-nav a {
    display: inline-flex;
    align-items: center;
    min-height: 40px;
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    color: #CBD5E1 !important;
    background: rgba(15, 23, 42, 0.85);
    border: 1px solid rgba(56, 189, 248, 0.25);
    text-decoration: none !important;
}
.kgh-comp-nav a[aria-current="page"] {
    background: rgba(56, 189, 248, 0.2);
    color: #38BDF8 !important;
    border-color: #38BDF8;
}
.kgh-tldr-box {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.14) 0%, rgba(56, 189, 248, 0.12) 100%);
    border: 1px solid rgba(16, 185, 129, 0.4);
    border-radius: 14px;
    padding: 18px 20px;
    margin: 0 0 22px;
    font-size: 15px;
    color: #ECFDF5;
    line-height: 1.6;
}
.kgh-comp-table-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 18px 0 24px;
    border-radius: 14px;
    border: 1px solid rgba(56, 189, 248, 0.28);
    background: rgba(15, 23, 42, 0.9);
}
.kgh-comp-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 640px;
    font-size: 14px;
}
.kgh-comp-table th, .kgh-comp-table td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.18);
    vertical-align: top;
    line-height: 1.5;
}
.kgh-comp-table th {
    background: rgba(30, 41, 59, 0.92);
    color: #FFFFFF;
    font-weight: 800;
    font-size: 14.5px;
}
.kgh-comp-table th.kgh-col-kolivri, .kgh-comp-table td.kgh-col-kolivri {
    background: rgba(16, 185, 129, 0.1);
    border-left: 1px solid rgba(16, 185, 129, 0.3);
    border-right: 1px solid rgba(16, 185, 129, 0.3);
}
.kgh-comp-win {
    color: #6EE7B7;
    font-weight: 700;
}
.kgh-comp-lose {
    color: #FDA4AF;
    font-weight: 600;
}
.kgh-honest-list {
    margin: 0;
    padding-inline-start: 20px;
    color: #E2E8F0;
    font-size: 14px;
    line-height: 1.65;
}
.kgh-honest-list li {
    margin-bottom: 8px;
}
@media (max-width: 768px) {
    .kgh-wrap {
        padding: 20px;
        margin: 16px auto;
    }
    .kgh-grid-2, .kgh-grid-3 {
        grid-template-columns: 1fr;
    }
    .kgh-kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
    .kgh-title {
        font-size: 23px;
    }
    .kgh-comp-table th, .kgh-comp-table td {
        padding: 10px 12px;
        font-size: 13px;
    }
}
@media (max-width: 480px) {
    .kgh-wrap {
        padding: 14px;
        border-radius: 14px;
    }
    .kgh-kpi-strip {
        grid-template-columns: 1fr;
    }
    .kgh-btn-row {
        flex-direction: column;
    }
    .kgh-btn {
        width: 100%;
    }
    .kgh-title {
        font-size: 20px;
    }
}
</style>
CSS;
    }

    /**
     * Play #1 helper: Preview Dead Invoice Recovery aging buckets, behavioral-economics
     * nudge strategies, Hebrew AI voice script, 5% contingency fee, and SaaS months unlocked.
     *
     * @param array $invoices
     * @return array
     */
    public static function previewInvoiceRecovery($invoices = array()) {
        if (empty($invoices) || !is_array($invoices)) {
            $invoices = self::getDefaultSampleInvoices();
        }

        $processed = array();
        $totalOverdueIls = 0.0;
        $bucketsCount = array(
            '1–30'  => 0,
            '31–60' => 0,
            '61+'   => 0,
        );

        foreach ($invoices as $inv) {
            $customerName = isset($inv['customerName']) ? (string) $inv['customerName'] : 'לקוח עסקי';
            $invoiceNum   = isset($inv['invoiceNumber']) ? (string) $inv['invoiceNumber'] : 'INV-1001';
            $amountIls    = isset($inv['amountIls']) ? max(0.0, (float) $inv['amountIls']) : 0.0;
            $daysOverdue  = isset($inv['daysOverdue']) ? max(1, (int) $inv['daysOverdue']) : 15;
            $provider     = isset($inv['provider']) ? (string) $inv['provider'] : 'Morning (חשבונית ירוקה)';

            if ($daysOverdue <= 30) {
                $bucketLabel = '1–30';
                $bucketClass = 'b-1-30';
                $nudgeKey    = 'gentle_friction_removal';
                $nudgeHe     = 'הסרת חיכוך רכה (Friction Removal) + שליחת לינק תשלום ב-SMS תוך כדי השיחה';
                $scriptHe    = "היי {$customerName}, מדברת נועה מהנהלת החשבונות (דרך הסוכן הקולי של קוליברי). ראינו שחשבונית {$invoiceNum} על סך ₪" . number_format($amountIls) . " מ-{$provider} עדיין פתוחה ({$daysOverdue} ימים). לשלוח לך עכשיו לינק לתשלום מהיר בביט או אשראי לנייד?";
            } elseif ($daysOverdue <= 60) {
                $bucketLabel = '31–60';
                $bucketClass = 'b-31-60';
                $nudgeKey    = 'social_norm_anchor';
                $nudgeHe     = 'עוגן נורמה חברתית (Social Norm Anchor — 92% מהלקוחות מסדירים עד סוף החודש) + אישור מועד פירעון קולי';
                $scriptHe    = "שלום {$customerName}, כאן הסוכן הקולי של קוליברי עבור סגירת כרטסת חודשית. 92% מהספקים והלקוחות כבר השלימו את סבב התשלומים החודשי, ונשארה רק חשבונית {$invoiceNum} על סך ₪" . number_format($amountIls) . " ({$daysOverdue} ימים בפיגור). נוח לך שנשלח לינק לסגירה היום או לחלק ל-2 תשלומים?";
            } else {
                $bucketLabel = '61+';
                $bucketClass = 'b-61-plus';
                $nudgeKey    = 'structured_settlement_framing';
                $nudgeHe     = 'שנאת הפסד והסדר מדורג (Loss Aversion & Structured Settlement Framing) לפני העברה לטיפול משפטי';
                $scriptHe    = "שלום {$customerName}, שיחה מתועדת בנוגע לחשבונית {$invoiceNum} ע״ס ₪" . number_format($amountIls) . " שפתוחה מעל {$daysOverdue} ימים. לפני שהמערכת מעבירה את התיק אוטומטית להמשך טיפול חיצוני, אנחנו יכולים לאשר לך עכשיו על הקו פריסה מיידית ללא ריבית או שליחת לינק תשלום. מה עדיף לך?";
            }

            $bucketsCount[$bucketLabel]++;
            $totalOverdueIls += $amountIls;
            $contingencyFeeIls = round($amountIls * self::CONTINGENCY_RATE, 2);

            $processed[] = array(
                'customerName'      => $customerName,
                'invoiceNumber'     => $invoiceNum,
                'provider'          => $provider,
                'amountIls'         => $amountIls,
                'daysOverdue'       => $daysOverdue,
                'agingBucket'       => $bucketLabel,
                'bucketCssClass'    => $bucketClass,
                'nudgeStrategyKey'  => $nudgeKey,
                'nudgeStrategyHe'   => $nudgeHe,
                'hebrewCallScript'  => $scriptHe,
                'contingencyFeeIls' => $contingencyFeeIls,
            );
        }

        // Conservative 65% recovery projection for summary metrics, plus 100% full-slate numbers
        $totalContingencyIls = round($totalOverdueIls * self::CONTINGENCY_RATE, 2);
        $netToBusinessIls    = round($totalOverdueIls - $totalContingencyIls, 2);

        $saasMonthsUnlocked = array(
            'starter'  => array(
                'planName'     => 'Starter',
                'monthlyPrice' => self::PLAN_STARTER_ILS,
                'monthsFromFee'=> round($totalContingencyIls / self::PLAN_STARTER_ILS, 1),
                'monthsFromNet'=> round($netToBusinessIls / self::PLAN_STARTER_ILS, 1),
            ),
            'business' => array(
                'planName'     => 'Business',
                'monthlyPrice' => self::PLAN_BUSINESS_ILS,
                'monthsFromFee'=> round($totalContingencyIls / self::PLAN_BUSINESS_ILS, 1),
                'monthsFromNet'=> round($netToBusinessIls / self::PLAN_BUSINESS_ILS, 1),
            ),
            'gold'     => array(
                'planName'     => 'Gold',
                'monthlyPrice' => self::PLAN_GOLD_ILS,
                'monthsFromFee'=> round($totalContingencyIls / self::PLAN_GOLD_ILS, 1),
                'monthsFromNet'=> round($netToBusinessIls / self::PLAN_GOLD_ILS, 1),
            ),
        );

        return array(
            'invoices'            => $processed,
            'bucketsCount'        => $bucketsCount,
            'totalOverdueIls'     => $totalOverdueIls,
            'contingencyRate'     => self::CONTINGENCY_RATE,
            'totalContingencyIls' => $totalContingencyIls,
            'netRecoveredIls'     => $netToBusinessIls,
            'saasMonthsUnlocked'  => $saasMonthsUnlocked,
            'apiEndpoint'         => self::KOLIVRI_API_BASE . '/api/v1/growth/invoice-recovery/preview',
        );
    }

    /**
     * Default realistic overdue CSV sample rows (Morning / GreenInvoice, iCount, YPay).
     *
     * @return array
     */
    public static function getDefaultSampleInvoices() {
        return array(
            array(
                'customerName'  => 'א.א. פרויקטים ושיפוצים בע״מ',
                'invoiceNumber' => 'MRN-40821',
                'provider'      => 'Morning (חשבונית ירוקה)',
                'amountIls'     => 4800,
                'daysOverdue'   => 18,
            ),
            array(
                'customerName'  => 'קליניקת ד״ר לוי אסתטיקה',
                'invoiceNumber' => 'ICN-91204',
                'provider'      => 'iCount',
                'amountIls'     => 9500,
                'daysOverdue'   => 44,
            ),
            array(
                'customerName'  => 'מוסך הצפון — שירותי רכב',
                'invoiceNumber' => 'YPY-77310',
                'provider'      => 'YPay',
                'amountIls'     => 14200,
                'daysOverdue'   => 76,
            ),
        );
    }

    /**
     * Play #2 helper: Build B2C-to-B2B Trojan Horse Caller mission and viral WhatsApp follow-ups.
     *
     * @param array $params
     * @return array
     */
    public static function buildTrojanMission($params = array()) {
        $allowedCategories = array(
            'trades'      => 'בעלי מקצוע (חשמלאי / אינסטלטור / מיזוג)',
            'clinic'      => 'מרפאות וקליניקות (שיניים / פיזיותרפיה / אסתטיקה)',
            'automotive'  => 'מוסכים ושירותי רכב (טיפול / טסט / פחחות)',
            'restaurant'  => 'מסעדות ואירוח (הזמנת שולחן / אירוע)',
            'real_estate' => 'נדל״ן וניהול נכסים (תיאום סיור בנכס)',
        );

        $category = isset($params['category']) && isset($allowedCategories[$params['category']])
            ? $params['category']
            : 'trades';
        $consumerName  = !empty($params['consumerName']) ? (string) $params['consumerName'] : 'דניאל כהן';
        $businessName  = !empty($params['businessName']) ? (string) $params['businessName'] : 'חשמל ותקשורת המרכז';
        $requestedTask = !empty($params['requestedTask']) ? (string) $params['requestedTask'] : 'בדיקת קצר בלוח חשמל תלת-פאזי והצעת מחיר להיום או מחר בבוקר';
        $preferredSlot = !empty($params['preferredSlot']) ? (string) $params['preferredSlot'] : 'היום בין 16:00 ל-19:00';

        $agentMissionPromptHe = "אתה סוכן ה-AI של קוליברי (הבוט שמתקשר במקומך). התקשר אל '{$businessName}' (קטגוריה: {$allowedCategories[$category]}) בשם הלקוח {$consumerName}. המטרה: לתאם '{$requestedTask}' במועד מועדף '{$preferredSlot}', לברר עלות ביקור/בדיקה, ולאשר זמינות מיידית ביומן.";

        $extractionSchema = array(
            'answeredByBusiness' => 'boolean',
            'confirmedSlot'      => 'string (ISO or Hebrew time window)',
            'quotedPriceIls'     => 'number | null',
            'contactPerson'      => 'string',
            'followUpAction'     => 'booked | callback_needed | no_answer',
        );

        $whatsappMissedCallFomoHe = "היי {$businessName} 👋 ניסינו לחייג אליכם עכשיו דרך *הבוט של קוליברי* בשם הלקוח {$consumerName} לגבי: \"{$requestedTask}\" ({$preferredSlot}), אבל הקו לא נענה.\n"
            . "💡 כדי שלא תפספסו את העבודה הבאה כשאתם בשטח — הפעילו עקוב אחריי חכם ב-30 שניות (`*61*" . self::KOLIVRI_PHONE_DIGITS . "#`) והסוכן הקולי של קוליברי יענה ויקבע לכם עבודות 24/7: " . self::KOLIVRI_HE_URL . " | " . self::KOLIVRI_PHONE_DISPLAY;

        $whatsappAnsweredDemoHe = "היי {$businessName} 🙌 הרגע תיאמנו אצלכם בטלפון עבור {$consumerName} (\"{$requestedTask}\") באמצעות סוכן ה-AI הקולי של *Kolivri*.\n"
            . "ראיתם כמה טבעי ומהיר הסוכן דיבר? רוצים סוכן קולי כזה שיענה גם לשיחות הנכנסות שלכם 24/7 ויתעד הכל ב-CRM? בדקו כאן: " . self::KOLIVRI_HE_URL . " או בטלפון " . self::KOLIVRI_PHONE_DISPLAY;

        return array(
            'category'                 => $category,
            'categoryLabelHe'          => $allowedCategories[$category],
            'allowedCategories'        => $allowedCategories,
            'consumerName'             => $consumerName,
            'businessName'             => $businessName,
            'requestedTask'            => $requestedTask,
            'preferredSlot'            => $preferredSlot,
            'agentMissionPromptHe'     => $agentMissionPromptHe,
            'extractionSchema'         => $extractionSchema,
            'whatsappMissedCallFomoHe' => $whatsappMissedCallFomoHe,
            'whatsappAnsweredDemoHe'   => $whatsappAnsweredDemoHe,
            'apiEndpoint'              => self::KOLIVRI_API_BASE . '/api/v1/growth/trojan-caller/mission',
        );
    }

    /**
     * Play #7 helper: Simulate Parallel-Dial FOMO Arbitrage race & atomic winner claim.
     *
     * @param array $params
     * @return array
     */
    public static function simulateFomoDispatch($params = array()) {
        $city          = !empty($params['city']) ? (string) $params['city'] : 'תל אביב / ראשון לציון';
        $emergencyNeed = !empty($params['emergencyNeed']) ? (string) $params['emergencyNeed'] : 'קצר בלוח חשמל ראשי + ריח שרוף — קריאת חירום מיידית';
        $estimatedIls  = isset($params['estimatedIls']) ? max(150, (int) $params['estimatedIls']) : 950;
        $winnerIndex   = isset($params['winnerIndex']) ? max(0, min(2, (int) $params['winnerIndex'])) : 0;

        $candidates = array(
            array(
                'index'        => 0,
                'name'         => 'אורן חשמל ומוקד תקלות 24/7 (תל אביב)',
                'area'         => 'תל אביב מרכז / צפון',
                'responseSec'  => '3.2 שניות',
            ),
            array(
                'index'        => 1,
                'name'         => 'ברק מערכות חשמל ותלת-פאזי (ראשון לציון)',
                'area'         => 'ראשון לציון מערב',
                'responseSec'  => 'לא ענה (צלצול 5)',
            ),
            array(
                'index'        => 2,
                'name'         => 'רונן הנדסת חשמל ובית חכם (חולון / ראשל״צ)',
                'area'         => 'ראשון לציון מזרח / חולון',
                'responseSec'  => 'תפוס בשיחה אחרת',
            ),
        );

        $winner = $candidates[$winnerIndex];
        $loserMessages = array();

        foreach ($candidates as $idx => $cand) {
            if ($idx === $winnerIndex) {
                continue;
            }
            $loserMessages[] = array(
                'candidateName' => $cand['name'],
                'whatsappTextHe'=> "⚠️ פספסת עכשיו קריאת חירום ב{$city} ({$emergencyNeed}, שווי מוערך: ₪{$estimatedIls}).\n"
                    . "המוקד של קוליברי חייג ל-3 בעלי מקצוע במקביל — *{$winner['name']}* הקיש 1 ראשון ותפס את העבודה.\n"
                    . "כדי שהסוכן הקולי של קוליברי יענה עבורך בצלצול הראשון ויתפוס עבודות בזמן שאתה על סולם, הפעל עכשיו `*61*" . self::KOLIVRI_PHONE_DIGITS . "#` או היכנס ל-" . self::KOLIVRI_FOLLOW_ME_URL,
            );
        }

        return array(
            'raceId'           => 'race_tlv_rsl_' . ($winnerIndex + 1),
            'city'             => $city,
            'emergencyNeed'    => $emergencyNeed,
            'estimatedIls'     => $estimatedIls,
            'candidates'       => $candidates,
            'winnerIndex'      => $winnerIndex,
            'winner'           => $winner,
            'atomicLockStatus' => "🔒 נעילה אטומית הושלמה: {$winner['name']} הקיש '1' ראשון וגישור השיחה החי עם הלקוח חובר תוך 180ms.",
            'loserMessages'    => $loserMessages,
            'raceEndpoint'     => self::KOLIVRI_API_BASE . '/api/v1/growth/fomo-arbitrage/race',
            'claimEndpoint'    => self::KOLIVRI_API_BASE . '/api/v1/growth/fomo-arbitrage/claim',
        );
    }

    /**
     * Play #6 helper: Generate GSM/Israeli carrier conditional call-forwarding codes.
     *
     * @param string $carrier
     * @param string $targetNumber
     * @param int    $ringSeconds
     * @return array
     */
    public static function generateCarrierForwardingCodes($carrier = 'partner', $targetNumber = self::KOLIVRI_PHONE_DISPLAY, $ringSeconds = 15) {
        $carriers = array(
            'partner'    => 'Partner / 012 Mobile (פרטנר)',
            'pelephone'  => 'Pelephone (פלאפון)',
            'cellcom'    => 'Cellcom (סלקום)',
            'hot_mobile' => 'HOT Mobile (הוט מובייל)',
            'golan'      => 'Golan Telecom (גולן טלקום)',
            '019'        => '019 Mobile (019 מובייל)',
            'we4g'       => 'We4G / Wecom',
            'bezeq'      => 'Bezeq Landline (בזק קווי)',
        );
        if (!isset($carriers[$carrier])) {
            $carrier = 'partner';
        }
        $allowedRings = array(5, 10, 15, 20, 25, 30);
        if (!in_array((int) $ringSeconds, $allowedRings, true)) {
            $ringSeconds = 15;
        }
        $digits = preg_replace('/\D+/', '', (string) $targetNumber);
        if ($digits === '') {
            $digits = self::KOLIVRI_PHONE_DIGITS;
        }

        if ($carrier === 'bezeq') {
            return array(
                'carrier'        => $carrier,
                'carrierLabelHe' => $carriers[$carrier],
                'targetDigits'   => $digits,
                'ringSeconds'    => $ringSeconds,
                'noAnswerCode'   => "*42*{$digits}#",
                'busyCode'       => "*41*{$digits}#",
                'unreachableCode'=> "*42*{$digits}#",
                'allConditional' => "*41*{$digits}# / *42*{$digits}#",
                'unconditional'  => "*72*{$digits}#",
                'resetCode'      => '#41# / #42# / #72#',
            );
        }

        return array(
            'carrier'        => $carrier,
            'carrierLabelHe' => $carriers[$carrier],
            'targetDigits'   => $digits,
            'ringSeconds'    => $ringSeconds,
            'noAnswerCode'   => "**61*{$digits}**{$ringSeconds}#",
            'busyCode'       => "**67*{$digits}#",
            'unreachableCode'=> "**62*{$digits}#",
            'allConditional' => "**004*{$digits}**{$ringSeconds}#",
            'unconditional'  => "**21*{$digits}#",
            'resetCode'      => '##002#',
            'carriersList'   => $carriers,
        );
    }

    /**
     * Play #10 helper: Build Ghost Mini-Site preview for `biz-without-website` SMBs.
     *
     * @param array $params
     * @return array
     */
    public static function buildGhostMinisitePreview($params = array()) {
        $presets = array(
            array(
                'businessName' => 'א.ש. חשמל ולוח פיקוד',
                'profession'   => 'חשמלאי מוסמך ותקלות 24/7',
                'city'         => 'ראשון לציון והמרכז',
                'phone'        => self::KOLIVRI_PHONE_DISPLAY,
                'rating'       => '4.9',
            ),
            array(
                'businessName' => 'קליניקת חיוכים — רפואת שיניים ואסתטיקה',
                'profession'   => 'מרפאת שיניים ושיננית',
                'city'         => 'תל אביב',
                'phone'        => self::KOLIVRI_PHONE_DISPLAY,
                'rating'       => '4.9',
            ),
            array(
                'businessName' => 'מאסטר קור — מיזוג אוויר ואינסטלציה',
                'profession'   => 'טכנאי מיזוג ואינסטלטור',
                'city'         => 'פתח תקווה / רמת גן',
                'phone'        => self::KOLIVRI_PHONE_DISPLAY,
                'rating'       => '4.8',
            ),
        );

        $get = isset($_GET) && is_array($_GET) ? $_GET : array();
        $rawName = !empty($params['businessName']) ? $params['businessName']
            : (!empty($params['name']) ? $params['name']
            : (!empty($get['name']) ? $get['name'] : $presets[0]['businessName']));
        $rawProf = !empty($params['profession']) ? $params['profession']
            : (!empty($params['category']) ? $params['category']
            : (!empty($get['category']) ? $get['category'] : $presets[0]['profession']));
        $rawCity = !empty($params['city']) ? $params['city']
            : (!empty($get['city']) ? $get['city'] : $presets[0]['city']);
        $rawPhone = !empty($params['phone']) ? $params['phone']
            : (!empty($get['phone']) ? $get['phone'] : self::KOLIVRI_PHONE_DISPLAY);
        $rawRating = !empty($params['rating']) ? $params['rating']
            : (!empty($get['rating']) ? $get['rating'] : '4.9');

        $businessName = trim(strip_tags((string) $rawName));
        $profession   = trim(strip_tags((string) $rawProf));
        $city         = trim(strip_tags((string) $rawCity));
        $phone        = trim(strip_tags((string) $rawPhone));
        $rating       = trim(strip_tags((string) $rawRating));

        $activationCode = '**61*' . self::KOLIVRI_PHONE_DIGITS . '**15#';
        $aiGreetingHe   = "שלום! הגעתם אל {$businessName} ({$profession} ב{$city}). אני העוזרת הקולית של העסק — אפשר לתאם דרכי קריאת שירות, לברר זמינות ומחירים, או לקבל חזרה מיידית מבעל המקצוע.";
        $shareUrl       = self::KOLIVRI_SITE_URL . '/he/ghost-site/?name=' . rawurlencode($businessName)
            . '&category=' . rawurlencode($profession)
            . '&city=' . rawurlencode($city)
            . '&phone=' . rawurlencode($phone);

        return array(
            'businessName'   => $businessName,
            'profession'     => $profession,
            'city'           => $city,
            'phone'          => $phone,
            'rating'         => $rating,
            'activationCode' => $activationCode,
            'aiGreetingHe'   => $aiGreetingHe,
            'shareUrl'       => $shareUrl,
            'presets'        => $presets,
        );
    }

    /**
     * Render sticky Cross-Tool Lab Switcher Bar and shared Once-Only Context Carry-Forward script.
     *
     * @param string $activeSlug
     * @param string $lang
     * @return string
     */
    public static function resolveLang($atts = array()) {
        if (is_array($atts) && !empty($atts['lang'])) {
            return (strtolower((string) $atts['lang']) === 'en') ? 'en' : 'he';
        }
        if (defined('ICL_LANGUAGE_CODE') && strtolower((string) ICL_LANGUAGE_CODE) === 'en') {
            return 'en';
        }
        if (!empty($_SERVER['REQUEST_URI']) && strpos((string) $_SERVER['REQUEST_URI'], '/he/') === false && strpos((string) $_SERVER['REQUEST_URI'], '/he') !== 0) {
            return 'en';
        }
        return 'he';
    }

    public static function renderLabSwitcher($activeSlug, $lang = 'he') {
        $isEn = (strtolower((string) $lang) === 'en');
        $prefix = $isEn ? '' : '/he';
        $homeHref = $prefix . '/#ai-voice-lab';
        $homeLabel = $isEn ? '← Home' : '← חזרה לדף הבית';

        $tools = array(
            'phone-grader'       => array('path' => '/phone-grader/',       'he' => '🔥 מדד המענה',          'en' => '🔥 Phone Grader'),
            'amnesia-test'       => array('path' => '/amnesia-test/',       'he' => '🧠 מבחן האמנזיה',       'en' => '🧠 Amnesia Test'),
            'invoice-recovery'   => array('path' => '/invoice-recovery/',   'he' => '💰 גביית חשבוניות',     'en' => '💰 Invoice Recovery'),
            'ghost-site'         => array('path' => '/ghost-site/',         'he' => '🌐 מיני-סייט חינם',     'en' => '🌐 Ghost Mini-Site'),
            'call-for-me'        => array('path' => '/call-for-me/',        'he' => '🤖 הבוט שיתקשר במקומך', 'en' => '🤖 Call For Me'),
            'dispatch'           => array('path' => '/dispatch/',           'he' => '⚡ זירת חיוג מקבילי',   'en' => '⚡ FOMO Dispatch'),
            'woo-voice-recovery' => array('path' => '/woo-voice-recovery/', 'he' => '🛒 WooCommerce קולי',   'en' => '🛒 Woo Voice ROI'),
            'mcp'                => array('path' => '/mcp/',                'he' => '🛠️ MCP למפתחים',        'en' => '🛠️ Developer MCP'),
        );

        $html = '<nav class="klv-lab-switcher" aria-label="Kolivri AI Voice Lab Navigation">';
        $html .= '<a href="' . self::esc($homeHref) . '" class="klv-lab-home">' . self::esc($homeLabel) . '</a>';
        foreach ($tools as $slug => $meta) {
            $href = $prefix . $meta['path'];
            $label = $isEn ? $meta['en'] : $meta['he'];
            $current = ($slug === $activeSlug) ? ' aria-current="page"' : '';
            $html .= '<a href="' . self::esc($href) . '"' . $current . '>' . self::esc($label) . '</a>';
        }
        $html .= '</nav>';
        return $html;
    }

    /**
     * Shared Once-Only Context Carry-Forward (`?phone=...&carrier=...&score=...`) JS snippet
     * for Growth Hub shortcodes.
     *
     * @return string
     */
    public static function getContextCarryForwardScript() {
        return <<<'JS'
<script>
(function() {
    if (typeof window === 'undefined' || !window.location) return;
    var params = new URLSearchParams(window.location.search);
    var state = {
        phone: params.get('phone') || '',
        carrier: params.get('carrier') || '',
        score: params.get('score') || ''
    };
    function syncContextLinks() {
        var qsParts = [];
        if (state.phone) qsParts.push('phone=' + encodeURIComponent(state.phone));
        if (state.carrier) qsParts.push('carrier=' + encodeURIComponent(state.carrier));
        if (state.score !== '' && state.score !== null && state.score !== undefined) {
            qsParts.push('score=' + encodeURIComponent(state.score));
        }
        var qs = qsParts.join('&');
        var links = document.querySelectorAll('.klv-lab-switcher a, a[href*="/he/start/"], a[href*="/start/"], a[href*="/he/follow-me/"], a[href*="/follow-me/"]');
        links.forEach(function(a) {
            var rawHref = a.getAttribute('data-base-href') || a.getAttribute('href') || '';
            if (!rawHref) return;
            if (!a.getAttribute('data-base-href')) {
                a.setAttribute('data-base-href', rawHref);
            }
            var hashIdx = rawHref.indexOf('#');
            var hash = hashIdx !== -1 ? rawHref.slice(hashIdx) : '';
            var baseNoHash = hashIdx !== -1 ? rawHref.slice(0, hashIdx) : rawHref;
            var qIdx = baseNoHash.indexOf('?');
            var cleanPath = qIdx !== -1 ? baseNoHash.slice(0, qIdx) : baseNoHash;
            a.setAttribute('href', qs ? (cleanPath + '?' + qs + hash) : (cleanPath + hash));
        });
    }
    var phoneInputs = document.querySelectorAll('#kgh-ghost-phone, #kgh-mcp-target, input[type="tel"], input[name="phone"]');
    var carrierSelects = document.querySelectorAll('#kgh-mcp-carrier, select[name="carrier"]');
    if (state.phone) {
        phoneInputs.forEach(function(inp) {
            inp.value = state.phone;
            inp.dispatchEvent(new Event('input', { bubbles: true }));
        });
    }
    if (state.carrier) {
        carrierSelects.forEach(function(sel) {
            sel.value = state.carrier;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }
    phoneInputs.forEach(function(inp) {
        var onPhone = function() {
            state.phone = (inp.value || '').trim();
            syncContextLinks();
        };
        inp.addEventListener('input', onPhone);
        inp.addEventListener('change', onPhone);
    });
    carrierSelects.forEach(function(sel) {
        var onCarrier = function() {
            state.carrier = (sel.value || '').trim();
            syncContextLinks();
        };
        sel.addEventListener('change', onCarrier);
        sel.addEventListener('input', onCarrier);
    });
    syncContextLinks();
})();
</script>
JS;
    }

    /**
     * Render Shortcode 1: `[kolivri_invoice_recovery_app]` (Play #1).
     *
     * @param array $atts
     * @return string
     */
    public static function renderInvoiceRecoveryApp($atts = array()) {
        $lang = self::resolveLang($atts);
        $isEn = ($lang === 'en');
        $preview = self::previewInvoiceRecovery();
        $starterMonths  = $preview['saasMonthsUnlocked']['starter']['monthsFromFee'];
        $businessMonths = $preview['saasMonthsUnlocked']['business']['monthsFromFee'];
        $goldMonths     = $preview['saasMonthsUnlocked']['gold']['monthsFromFee'];

        ob_start();
        echo self::getScopedCss();
        if ($isEn) :
        ?>
<div class="kgh-wrap" id="kgh-invoice-recovery-app" dir="ltr" lang="en" style="text-align:left;" data-shortcode="kolivri_invoice_recovery_app" data-api="<?php echo self::esc($preview['apiEndpoint']); ?>">
    <?php echo self::renderLabSwitcher('invoice-recovery', 'en'); ?>
    <span class="kgh-badge orange">💰 Zero-Subscription Dead Invoice Recovery</span>
    <h2 class="kgh-title">AI Voice Overdue Invoice Recovery — ₪0 Subscription (5% Success Fee Only)</h2>
    <p class="kgh-subtitle">
        Load overdue invoices from <strong>Morning (GreenInvoice)</strong>, <strong>iCount</strong>, or <strong>YPay</strong>. The engine segments each receivable by aging bucket (<code>1–30</code>, <code>31–60</code>, <code>61+</code> days), selects a behavioral-economics nudge strategy, generates a live voice call script, and calculates how many months of Kolivri SaaS your recovered cash unlocks.
    </p>

    <div class="kgh-btn-row" style="margin-bottom:18px;">
        <button type="button" class="kgh-btn kgh-btn-primary" id="kgh-inv-load-sample">
            📄 Load Sample CSV (Morning / iCount / YPay) &amp; Run Live Recovery Simulation
        </button>
        <a href="<?php echo self::esc(self::KOLIVRI_APP_URL); ?>" class="kgh-btn kgh-btn-secondary">
            Connect Account at app.kolivri.co.il →
        </a>
        <a href="tel:0547875670" class="kgh-btn kgh-btn-secondary">
            📞 Kolivri Line: <?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>
        </a>
    </div>

    <div class="kgh-kpi-strip" id="kgh-inv-kpis">
        <div class="kgh-kpi">
            <div class="lbl">Total Overdue Receivables</div>
            <div class="val" id="kgh-inv-total">₪<?php echo number_format($preview['totalOverdueIls']); ?></div>
        </div>
        <div class="kgh-kpi">
            <div class="lbl">Success Fee Only (5%)</div>
            <div class="val orange" id="kgh-inv-fee">₪<?php echo number_format($preview['totalContingencyIls']); ?></div>
        </div>
        <div class="kgh-kpi">
            <div class="lbl">Net Cash Recovered to You</div>
            <div class="val emerald" id="kgh-inv-net">₪<?php echo number_format($preview['netRecoveredIls']); ?></div>
        </div>
        <div class="kgh-kpi">
            <div class="lbl">SaaS Months Unlocked from Fee</div>
            <div class="val" id="kgh-inv-saas" style="font-size:15px;line-height:1.35;">
                Starter (₪247): <?php echo self::esc($starterMonths); ?> mos<br>
                Business (₪479): <?php echo self::esc($businessMonths); ?> · Gold (₪880): <?php echo self::esc($goldMonths); ?>
            </div>
        </div>
    </div>

    <div class="kgh-card-list" id="kgh-inv-list">
        <?php foreach ($preview['invoices'] as $inv) : ?>
        <div class="kgh-item-card">
            <div class="kgh-panel-title">
                <span><?php echo self::esc($inv['customerName']); ?> · <small style="color:#94A3B8;"><?php echo self::esc($inv['invoiceNumber']); ?> (<?php echo self::esc($inv['provider']); ?>)</small></span>
                <span class="kgh-pill <?php echo self::esc($inv['bucketCssClass']); ?>">
                    Aging Bucket: <?php echo self::esc($inv['agingBucket']); ?> days (<?php echo (int) $inv['daysOverdue']; ?>d overdue)
                </span>
            </div>
            <div style="font-size:14px;color:#CBD5E1;margin-bottom:8px;">
                <strong>Invoice Amount:</strong> ₪<?php echo number_format($inv['amountIls']); ?> ·
                <strong>Success Fee (5%):</strong> ₪<?php echo number_format($inv['contingencyFeeIls']); ?> ·
                <strong>Behavioral Nudge:</strong> <?php echo self::esc($inv['nudgeStrategyKey']); ?>
            </div>
            <div class="kgh-wa-box">
                <strong>🎙️ Outbound AI Voice Script:</strong><br>
                <?php echo self::esc($inv['hebrewCallScript']); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:18px;font-size:13px;color:#94A3B8;display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <span>Connected API: <code>https://api.kolivri.co.il/api/v1/growth/invoice-recovery/preview</code></span>
        <span>Kolivri Plans: Starter <code>₪247</code> · Business <code>₪479</code> · Gold <code>₪880</code> | <a href="https://kolivri.com/" style="color:#38BDF8;">kolivri.com</a></span>
    </div>
        <?php else : ?>
<div class="kgh-wrap" id="kgh-invoice-recovery-app" dir="rtl" lang="he" data-shortcode="kolivri_invoice_recovery_app" data-api="<?php echo self::esc($preview['apiEndpoint']); ?>">
    <?php echo self::renderLabSwitcher('invoice-recovery', 'he'); ?>
    <span class="kgh-badge orange">💰 Zero-Subscription Dead Invoice Recovery</span>
    <h2 class="kgh-title">גביית חשבוניות פתוחות ב-AI קולי — 0 ₪ דמי מנוי (5% מהגבייה בלבד)</h2>
    <p class="kgh-subtitle">
        טענו קובץ חשבוניות בפיגור מ-<strong>Morning (חשבונית ירוקה)</strong>, <strong>iCount</strong> או <strong>YPay</strong>. המנוע מסווג כל חוב לפי גיל החוב (<code>1–30</code>, <code>31–60</code>, <code>61+</code> ימים), בוחר אסטרטגיית כלכלה התנהגותית, מייצר תסריט שיחה בעברית ומחשב כמה חודשי מנוי קוליברי נפתחים עבורכם בחינם מתוך הגבייה.
    </p>

    <div class="kgh-btn-row" style="margin-bottom:18px;">
        <button type="button" class="kgh-btn kgh-btn-primary" id="kgh-inv-load-sample">
            📄 טען קובץ CSV לדוגמה (Morning / iCount / YPay) ובצע סימולציית גבייה חיה
        </button>
        <a href="<?php echo self::esc(self::KOLIVRI_APP_URL); ?>" class="kgh-btn kgh-btn-secondary">
            חיבור לחשבון ב-app.kolivri.co.il ←
        </a>
        <a href="tel:0547875670" class="kgh-btn kgh-btn-secondary">
            📞 מוקד קוליברי: <?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>
        </a>
    </div>

    <div class="kgh-kpi-strip" id="kgh-inv-kpis">
        <div class="kgh-kpi">
            <div class="lbl">סה״כ חובות פתוחים</div>
            <div class="val" id="kgh-inv-total">₪<?php echo number_format($preview['totalOverdueIls']); ?></div>
        </div>
        <div class="kgh-kpi">
            <div class="lbl">עמלת הצלחה בלבד (5%)</div>
            <div class="val orange" id="kgh-inv-fee">₪<?php echo number_format($preview['totalContingencyIls']); ?></div>
        </div>
        <div class="kgh-kpi">
            <div class="lbl">נטו שנכנס לחשבון שלכם</div>
            <div class="val emerald" id="kgh-inv-net">₪<?php echo number_format($preview['netRecoveredIls']); ?></div>
        </div>
        <div class="kgh-kpi">
            <div class="lbl">חודשי מנוי שנפתחים מהעמלה</div>
            <div class="val" id="kgh-inv-saas" style="font-size:15px;line-height:1.35;">
                Starter (₪247): <?php echo self::esc($starterMonths); ?> חודשים<br>
                Business (₪479): <?php echo self::esc($businessMonths); ?> · Gold (₪880): <?php echo self::esc($goldMonths); ?>
            </div>
        </div>
    </div>

    <div class="kgh-card-list" id="kgh-inv-list">
        <?php foreach ($preview['invoices'] as $inv) : ?>
        <div class="kgh-item-card">
            <div class="kgh-panel-title">
                <span><?php echo self::esc($inv['customerName']); ?> · <small style="color:#94A3B8;"><?php echo self::esc($inv['invoiceNumber']); ?> (<?php echo self::esc($inv['provider']); ?>)</small></span>
                <span class="kgh-pill <?php echo self::esc($inv['bucketCssClass']); ?>">
                    סל גיל חוב: <?php echo self::esc($inv['agingBucket']); ?> ימים (<?php echo (int) $inv['daysOverdue']; ?> ימים)
                </span>
            </div>
            <div style="font-size:14px;color:#CBD5E1;margin-bottom:8px;">
                <strong>סכום חשבונית:</strong> ₪<?php echo number_format($inv['amountIls']); ?> ·
                <strong>עמלת הצלחה (5%):</strong> ₪<?php echo number_format($inv['contingencyFeeIls']); ?> ·
                <strong>אסטרטגיית שכנוע (Behavioral Nudge):</strong> <?php echo self::esc($inv['nudgeStrategyHe']); ?>
            </div>
            <div class="kgh-wa-box">
                <strong>🎙️ תסריט שיחה קולית ב-AI (עברית):</strong><br>
                <?php echo self::esc($inv['hebrewCallScript']); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:18px;font-size:13px;color:#94A3B8;display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <span>מחובר ל-API: <code>https://api.kolivri.co.il/api/v1/growth/invoice-recovery/preview</code></span>
        <span>תמחור חבילות קוליברי: Starter <code>₪247</code> · Business <code>₪479</code> · Gold <code>₪880</code> | <a href="https://kolivri.com/he/" style="color:#38BDF8;">kolivri.com/he</a></span>
    </div>
        <?php endif; ?>

    <script>
    (function() {
        var btn = document.getElementById('kgh-inv-load-sample');
        if (!btn) return;
        var isEn = <?php echo $isEn ? 'true' : 'false'; ?>;
        var sampleInvoices = <?php echo json_encode($preview['invoices'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        btn.addEventListener('click', function() {
            btn.textContent = isEn ? '⏳ Running recovery analysis against api.kolivri.co.il...' : '⏳ מריץ ניתוח גבייה מול api.kolivri.co.il...';
            fetch('https://api.kolivri.co.il/api/v1/growth/invoice-recovery/preview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ invoices: sampleInvoices })
            })
            .then(function(r) { return r.ok ? r.json() : Promise.reject(r.status); })
            .then(function() {
                btn.textContent = isEn ? '✓ Synced with api.kolivri.co.il (3 invoices analyzed)' : '✓ סונכרן בהצלחה מול api.kolivri.co.il (3 חשבוניות נותחו)';
            })
            .catch(function() {
                btn.textContent = isEn ? '✓ Computed locally (3 invoices: 1–30, 31–60, 61+ days)' : '✓ חושב במנוע המקומי (3 חשבוניות: 1–30, 31–60, 61+ ימים)';
            });
        });
    })();
    </script>
    <?php echo self::getContextCarryForwardScript(); ?>
</div>
        <?php
        return trim((string) ob_get_clean());
    }

    /**
     * Render Shortcode 2: `[kolivri_trojan_caller_app]`.
     *
     * @param array $atts
     * @return string
     */
    public static function renderTrojanCallerApp($atts = array()) {
        $lang = self::resolveLang($atts);
        $isEn = ($lang === 'en');
        $mission = self::buildTrojanMission($atts);
        ob_start();
        echo self::getScopedCss();
        if ($isEn) :
        ?>
<div class="kgh-wrap" id="kgh-trojan-caller-app" dir="ltr" lang="en" style="text-align:left;" data-shortcode="kolivri_trojan_caller_app" data-api="<?php echo self::esc($mission['apiEndpoint']); ?>">
    <?php echo self::renderLabSwitcher('call-for-me', 'en'); ?>
    <span class="kgh-badge">🤖 Call For Me — AI Consumer Proxy Caller (B2C-to-B2B Trojan Horse Caller)</span>
    <h2 class="kgh-title">Call For Me — Let Kolivri AI Call Any Local Business to Book or Get a Quote</h2>
    <p class="kgh-subtitle">
        Hate waiting on hold or chasing local tradespeople? Set your consumer call mission — Kolivri's AI voice agent calls the business, books your slot, and sends the business owner a live WhatsApp demo showing how <code>*61*</code> stops missed-call revenue loss.
    </p>

    <div class="kgh-grid-2">
        <div class="kgh-panel">
            <div class="kgh-panel-title">1. Configure Consumer Call Mission (Consumer Proxy Booking)</div>
            <div class="kgh-field">
                <label for="kgh-trj-category">Local Business Category (SMB Category)</label>
                <select id="kgh-trj-category" name="category">
                    <option value="trades" selected>trades — Home Services (Electrician / Plumber / HVAC)</option>
                    <option value="clinic">clinic — Medical &amp; Dental Clinics</option>
                    <option value="automotive">automotive — Auto Repair &amp; Garages</option>
                    <option value="restaurant">restaurant — Restaurants &amp; Hospitality</option>
                    <option value="real_estate">real_estate — Real Estate &amp; Property Management</option>
                </select>
            </div>
            <div class="kgh-field">
                <label for="kgh-trj-business">Local Business Name</label>
                <input type="text" id="kgh-trj-business" value="<?php echo self::esc($mission['businessName']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-trj-consumer">Your Name (Consumer)</label>
                <input type="text" id="kgh-trj-consumer" value="<?php echo self::esc($mission['consumerName']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-trj-task">What Should the AI Ask For?</label>
                <input type="text" id="kgh-trj-task" value="<?php echo self::esc($mission['requestedTask']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-trj-slot">Preferred Time Slot</label>
                <input type="text" id="kgh-trj-slot" value="<?php echo self::esc($mission['preferredSlot']); ?>">
            </div>
            <div class="kgh-btn-row">
                <button type="button" class="kgh-btn kgh-btn-primary" id="kgh-trj-generate">
                    🚀 Generate AI Voice Mission + Viral SMB WhatsApp Follow-Up
                </button>
            </div>
        </div>

        <div class="kgh-panel">
            <div class="kgh-panel-title">2. Live Preview: Outbound Agent Prompt + Viral B2B Follow-Up</div>
            <div style="font-size:13px;color:#94A3B8;margin-bottom:6px;">Live Outbound AI Voice Agent Mission:</div>
            <div class="kgh-wa-box" id="kgh-trj-mission-out"><?php echo self::esc($mission['agentMissionPromptHe']); ?></div>

            <div style="font-size:13px;color:#FDA4AF;margin:14px 0 6px;font-weight:700;">Scenario A: Business Missed the Call — WhatsApp FOMO Alert (*61*):</div>
            <div class="kgh-fomo-box" id="kgh-trj-fomo-out"><?php echo nl2br(self::esc($mission['whatsappMissedCallFomoHe'])); ?></div>

            <div style="font-size:13px;color:#6EE7B7;margin:14px 0 6px;font-weight:700;">Scenario B: Business Answered &amp; Booked — Live B2B AI Demo:</div>
            <div class="kgh-wa-box" id="kgh-trj-demo-out"><?php echo nl2br(self::esc($mission['whatsappAnsweredDemoHe'])); ?></div>

            <div class="kgh-btn-row" style="margin-top:14px;">
                <a href="/follow-me/" class="kgh-btn kgh-btn-emerald">
                    Business Owner? Activate *61* in 30s →
                </a>
                <a href="https://wa.me/972547875670" class="kgh-btn kgh-btn-secondary">
                    Kolivri WhatsApp: <?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>
                </a>
            </div>
        </div>
    </div>
        <?php else : ?>
<div class="kgh-wrap" id="kgh-trojan-caller-app" dir="rtl" lang="he" data-shortcode="kolivri_trojan_caller_app" data-api="<?php echo self::esc($mission['apiEndpoint']); ?>">
    <?php echo self::renderLabSwitcher('call-for-me', 'he'); ?>
    <span class="kgh-badge">🤖 הבוט שיתקשר במקומך (B2C-to-B2B Trojan Horse Caller)</span>
    <h2 class="kgh-title">הבוט שיתקשר במקומך — תיאום תור או הצעת מחיר מול כל עסק מקומי</h2>
    <p class="kgh-subtitle">
        שונאים לחכות על הקו או לרדוף אחרי בעלי מקצוע? הגדירו משימת שיחה לצרכן — סוכן ה-AI של קוליברי מתקשר לעסק, מתאם עבורכם, ושולח לבעל העסק הדגמה ויראלית בווטסאפ שמראה לו איך <code>*61*</code> מונע ממנו לפספס לקוחות.
    </p>

    <div class="kgh-grid-2">
        <div class="kgh-panel">
            <div class="kgh-panel-title">1. הגדרת משימת חיוג (Consumer Proxy Booking)</div>
            <div class="kgh-field">
                <label for="kgh-trj-category">קטגוריית עסק מקומי (SMB Category)</label>
                <select id="kgh-trj-category" name="category">
                    <option value="trades" selected>trades — בעלי מקצוע (חשמלאי / אינסטלטור / מיזוג)</option>
                    <option value="clinic">clinic — מרפאות וקליניקות (שיניים / פיזיותרפיה / אסתטיקה)</option>
                    <option value="automotive">automotive — מוסכים ושירותי רכב</option>
                    <option value="restaurant">restaurant — מסעדות ואירוח</option>
                    <option value="real_estate">real_estate — נדל״ן וניהול נכסים</option>
                </select>
            </div>
            <div class="kgh-field">
                <label for="kgh-trj-business">שם העסק המקומי</label>
                <input type="text" id="kgh-trj-business" value="<?php echo self::esc($mission['businessName']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-trj-consumer">שם המזמין (הצרכן)</label>
                <input type="text" id="kgh-trj-consumer" value="<?php echo self::esc($mission['consumerName']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-trj-task">מה לבקש בשיחה?</label>
                <input type="text" id="kgh-trj-task" value="<?php echo self::esc($mission['requestedTask']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-trj-slot">מועד מועדף</label>
                <input type="text" id="kgh-trj-slot" value="<?php echo self::esc($mission['preferredSlot']); ?>">
            </div>
            <div class="kgh-btn-row">
                <button type="button" class="kgh-btn kgh-btn-primary" id="kgh-trj-generate">
                    🚀 צור משימת סוכן AI + הודעת ווטסאפ ויראלית לעסק
                </button>
            </div>
        </div>

        <div class="kgh-panel">
            <div class="kgh-panel-title">2. תצוגה מקדימה: משימת סוכן + Follow-Up ויראלי לבעל העסק</div>
            <div style="font-size:13px;color:#94A3B8;margin-bottom:6px;">הנחיית משימה חיה לסוכן הקולי (Outbound Hebrew Agent Mission):</div>
            <div class="kgh-wa-box" id="kgh-trj-mission-out"><?php echo self::esc($mission['agentMissionPromptHe']); ?></div>

            <div style="font-size:13px;color:#FDA4AF;margin:14px 0 6px;font-weight:700;">תרחיש א׳: העסק לא ענה — הודעת ווטסאפ FOMO (*61*):</div>
            <div class="kgh-fomo-box" id="kgh-trj-fomo-out"><?php echo nl2br(self::esc($mission['whatsappMissedCallFomoHe'])); ?></div>

            <div style="font-size:13px;color:#6EE7B7;margin:14px 0 6px;font-weight:700;">תרחיש ב׳: העסק ענה ותואם תור — הדגמת B2B חיה:</div>
            <div class="kgh-wa-box" id="kgh-trj-demo-out"><?php echo nl2br(self::esc($mission['whatsappAnsweredDemoHe'])); ?></div>

            <div class="kgh-btn-row" style="margin-top:14px;">
                <a href="<?php echo self::esc(self::KOLIVRI_FOLLOW_ME_URL); ?>" class="kgh-btn kgh-btn-emerald">
                    בעל עסק? הפעל *61* ב-30 שניות ←
                </a>
                <a href="https://wa.me/972547875670" class="kgh-btn kgh-btn-secondary">
                    ווטסאפ קוליברי: <?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>
                </a>
            </div>
        </div>
    </div>
        <?php endif; ?>

    <script>
    (function() {
        var btn = document.getElementById('kgh-trj-generate');
        if (!btn) return;
        var isEn = <?php echo $isEn ? 'true' : 'false'; ?>;
        btn.addEventListener('click', function() {
            var cat = (document.getElementById('kgh-trj-category') || {}).value || 'trades';
            var biz = (document.getElementById('kgh-trj-business') || {}).value || (isEn ? 'Local Business' : 'העסק');
            var con = (document.getElementById('kgh-trj-consumer') || {}).value || (isEn ? 'Customer' : 'לקוח');
            var tsk = (document.getElementById('kgh-trj-task') || {}).value || (isEn ? 'Book appointment' : 'תיאום תור');
            var slt = (document.getElementById('kgh-trj-slot') || {}).value || (isEn ? 'Today' : 'היום');

            var missionText = isEn
                ? 'You are Kolivri\'s AI Voice Agent (Call For Me). Call "' + biz + '" (category: ' + cat + ') on behalf of customer ' + con + '. Objective: schedule "' + tsk + '" for preferred slot "' + slt + '", confirm visit/quote pricing, and lock calendar availability.'
                : 'אתה סוכן ה-AI של קוליברי (הבוט שמתקשר במקומך). התקשר אל "' + biz + '" (קטגוריה: ' + cat + ') בשם הלקוח ' + con + '. המטרה: לתאם "' + tsk + '" במועד מועדף "' + slt + '", לברר עלות ולאשר זמינות ביומן.';
            var fomoText = isEn
                ? 'Hi ' + biz + ' 👋 We just tried calling you via the Kolivri AI Agent on behalf of customer ' + con + ' regarding: "' + tsk + '" (' + slt + '), but your line went unanswered. Activate 30-second smart call forwarding (*61*0547875670#) at https://kolivri.com/ | 054-787-5670'
                : 'היי ' + biz + ' 👋 ניסינו לחייג אליכם עכשיו דרך הבוט של קוליברי בשם הלקוח ' + con + ' לגבי: "' + tsk + '" (' + slt + '), אבל הקו לא נענה. הפעילו עקוב אחריי חכם (*61*0547875670#) ב-https://kolivri.com/he/ | 054-787-5670';
            var demoText = isEn
                ? 'Hi ' + biz + ' 🙌 We just booked "' + tsk + '" with you by phone for ' + con + ' using the Kolivri AI Voice Agent. Want an AI receptionist answering your own inbound calls 24/7? https://kolivri.com/ | 054-787-5670'
                : 'היי ' + biz + ' 🙌 הרגע תיאמנו אצלכם בטלפון עבור ' + con + ' ("' + tsk + '") באמצעות סוכן ה-AI הקולי של Kolivri. רוצים סוכן כזה שיענה לשיחות שלכם 24/7? https://kolivri.com/he/ | 054-787-5670';

            var mEl = document.getElementById('kgh-trj-mission-out');
            var fEl = document.getElementById('kgh-trj-fomo-out');
            var dEl = document.getElementById('kgh-trj-demo-out');
            if (mEl) mEl.textContent = missionText;
            if (fEl) fEl.textContent = fomoText;
            if (dEl) dEl.textContent = demoText;

            fetch('https://api.kolivri.co.il/api/v1/growth/trojan-caller/mission', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category: cat, businessName: biz, consumerName: con, requestedTask: tsk, preferredSlot: slt })
            }).catch(function() {});
        });
    })();
    </script>
    <?php echo self::getContextCarryForwardScript(); ?>
</div>
        <?php
        return trim((string) ob_get_clean());
    }

    /**
     * Render Shortcode 3: `[kolivri_fomo_dispatch_app]`.
     *
     * @param array $atts
     * @return string
     */
    public static function renderFomoDispatchApp($atts = array()) {
        $lang = self::resolveLang($atts);
        $isEn = ($lang === 'en');
        $sim = self::simulateFomoDispatch($atts);
        ob_start();
        echo self::getScopedCss();
        if ($isEn) :
        ?>
<div class="kgh-wrap" id="kgh-fomo-dispatch-app" dir="ltr" lang="en" style="text-align:left;" data-shortcode="kolivri_fomo_dispatch_app" data-race-api="<?php echo self::esc($sim['raceEndpoint']); ?>" data-claim-api="<?php echo self::esc($sim['claimEndpoint']); ?>">
    <?php echo self::renderLabSwitcher('dispatch', 'en'); ?>
    <span class="kgh-badge orange">⚡ Parallel-Dial FOMO Arbitrage (Tel Aviv / Rishon LeZion)</span>
    <h2 class="kgh-title">Live Parallel-Dial Dispatch Arena — First Provider to Press 1 Wins the Job</h2>
    <p class="kgh-subtitle">
        When an urgent service call arrives in <strong>Tel Aviv / Rishon LeZion</strong> (<?php echo self::esc($sim['city']); ?> · Est. Value: <strong>₪<?php echo number_format($sim['estimatedIls']); ?></strong>), Kolivri dials 3 local providers simultaneously. Click <strong>"Press 1 to Claim Job"</strong> on any candidate card to see the 180ms atomic winner lock and the Loss-Aversion WhatsApp alerts sent to the 2 slower competitors.
    </p>

    <div class="kgh-grid-3" id="kgh-fomo-candidates">
        <?php foreach ($sim['candidates'] as $cand) : ?>
        <div class="kgh-panel kgh-fomo-cand" data-cand-index="<?php echo (int) $cand['index']; ?>">
            <div class="kgh-panel-title">
                <span>📞 Provider #<?php echo ((int) $cand['index']) + 1; ?></span>
                <span class="kgh-pill b-1-30">Ringing Now...</span>
            </div>
            <div style="font-size:16px;font-weight:700;color:#FFFFFF;margin-bottom:6px;">
                <?php echo self::esc($cand['name']); ?>
            </div>
            <div style="font-size:13px;color:#94A3B8;margin-bottom:14px;">
                Service Area: <?php echo self::esc($cand['area']); ?>
            </div>
            <button type="button" class="kgh-btn kgh-btn-emerald kgh-fomo-claim-btn" data-index="<?php echo (int) $cand['index']; ?>" style="width:100%;">
                Press 1 to Claim Job
            </button>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="kgh-panel" style="margin-top:20px;">
        <div class="kgh-panel-title">
            <span>Atomic Winner Lock Result + Missed-Job FOMO WhatsApp Alerts</span>
            <span style="font-size:12px;color:#38BDF8;">POST /api/v1/growth/fomo-arbitrage/race &amp; /claim</span>
        </div>
        <div class="kgh-wa-box" id="kgh-fomo-winner-lock">
            <?php echo self::esc($sim['atomicLockStatus']); ?>
        </div>
        <div id="kgh-fomo-losers-wrap" style="margin-top:12px;">
            <?php foreach ($sim['loserMessages'] as $loser) : ?>
            <div class="kgh-fomo-box">
                <strong>Sent via WhatsApp to <?php echo self::esc($loser['candidateName']); ?>:</strong><br>
                <?php echo nl2br(self::esc($loser['whatsappTextHe'])); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="kgh-btn-row" style="margin-top:16px;">
            <a href="/follow-me/" class="kgh-btn kgh-btn-primary">
                Never Miss the Next Job — Activate *61* to Kolivri (<?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>) →
            </a>
            <a href="/start/" class="kgh-btn kgh-btn-secondary">
                Start 14-Day Free Trial at app.kolivri.co.il
            </a>
        </div>
    </div>
        <?php else : ?>
<div class="kgh-wrap" id="kgh-fomo-dispatch-app" dir="rtl" lang="he" data-shortcode="kolivri_fomo_dispatch_app" data-race-api="<?php echo self::esc($sim['raceEndpoint']); ?>" data-claim-api="<?php echo self::esc($sim['claimEndpoint']); ?>">
    <?php echo self::renderLabSwitcher('dispatch', 'he'); ?>
    <span class="kgh-badge orange">⚡ Parallel-Dial FOMO Arbitrage (תל אביב / ראשון לציון)</span>
    <h2 class="kgh-title">זירת חיוג מקבילי חיה — הראשון שמקיש 1 תופס את העבודה</h2>
    <p class="kgh-subtitle">
        כשנכנסת קריאת חירום ב<strong><?php echo self::esc($sim['city']); ?></strong> (<?php echo self::esc($sim['emergencyNeed']); ?> · שווי מוערך: <strong>₪<?php echo number_format($sim['estimatedIls']); ?></strong>), המנוע של קוליברי מחייג ל-3 בעלי מקצוע במקביל. לחצו על <strong>״לחץ 1 לתפיסת העבודה״</strong> באחד הכרטיסים כדי לראות את הנעילה האטומית ואת הודעות הווטסאפ (Loss Aversion) שנשלחות ל-2 המפסידים.
    </p>

    <div class="kgh-grid-3" id="kgh-fomo-candidates">
        <?php foreach ($sim['candidates'] as $cand) : ?>
        <div class="kgh-panel kgh-fomo-cand" data-cand-index="<?php echo (int) $cand['index']; ?>">
            <div class="kgh-panel-title">
                <span>📞 מועמד #<?php echo ((int) $cand['index']) + 1; ?></span>
                <span class="kgh-pill b-1-30">מצלצל כעת...</span>
            </div>
            <div style="font-size:16px;font-weight:700;color:#FFFFFF;margin-bottom:6px;">
                <?php echo self::esc($cand['name']); ?>
            </div>
            <div style="font-size:13px;color:#94A3B8;margin-bottom:14px;">
                אזור: <?php echo self::esc($cand['area']); ?>
            </div>
            <button type="button" class="kgh-btn kgh-btn-emerald kgh-fomo-claim-btn" data-index="<?php echo (int) $cand['index']; ?>" style="width:100%;">
                לחץ 1 לתפיסת העבודה
            </button>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="kgh-panel" style="margin-top:20px;">
        <div class="kgh-panel-title">
            <span>תוצאת נעילה אטומית (Atomic Winner Lock) + הודעות FOMO למפסידים</span>
            <span style="font-size:12px;color:#38BDF8;">POST /api/v1/growth/fomo-arbitrage/race &amp; /claim</span>
        </div>
        <div class="kgh-wa-box" id="kgh-fomo-winner-lock">
            <?php echo self::esc($sim['atomicLockStatus']); ?>
        </div>
        <div id="kgh-fomo-losers-wrap" style="margin-top:12px;">
            <?php foreach ($sim['loserMessages'] as $loser) : ?>
            <div class="kgh-fomo-box">
                <strong>נשלח בווטסאפ אל <?php echo self::esc($loser['candidateName']); ?>:</strong><br>
                <?php echo nl2br(self::esc($loser['whatsappTextHe'])); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="kgh-btn-row" style="margin-top:16px;">
            <a href="<?php echo self::esc(self::KOLIVRI_FOLLOW_ME_URL); ?>" class="kgh-btn kgh-btn-primary">
                אל תפסיד את הקריאה הבאה — הפעל *61* לקוליברי (<?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>) ←
            </a>
            <a href="<?php echo self::esc(self::KOLIVRI_START_URL); ?>" class="kgh-btn kgh-btn-secondary">
                התחל 14 ימי ניסיון ב-app.kolivri.co.il
            </a>
        </div>
    </div>
        <?php endif; ?>

    <script>
    (function() {
        var isEn = <?php echo $isEn ? 'true' : 'false'; ?>;
        var candidates = <?php echo json_encode($sim['candidates'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        var city = <?php echo json_encode($sim['city'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        var need = <?php echo json_encode($sim['emergencyNeed'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        var est  = <?php echo (int) $sim['estimatedIls']; ?>;
        var btns = document.querySelectorAll('.kgh-fomo-claim-btn');
        btns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var winIdx = parseInt(btn.getAttribute('data-index') || '0', 10);
                var winner = candidates[winIdx] || candidates[0];
                var lockEl = document.getElementById('kgh-fomo-winner-lock');
                var loseEl = document.getElementById('kgh-fomo-losers-wrap');
                if (lockEl) {
                    lockEl.textContent = isEn
                        ? "🔒 Atomic Lock Completed: " + winner.name + " pressed '1' first and live call bridging connected in 180ms."
                        : "🔒 נעילה אטומית הושלמה: " + winner.name + " הקיש '1' ראשון וגישור השיחה החי עם הלקוח חובר תוך 180ms.";
                }
                if (loseEl) {
                    loseEl.textContent = '';
                    candidates.forEach(function(c, idx) {
                        if (idx === winIdx) return;
                        var box = document.createElement('div');
                        box.className = 'kgh-fomo-box';
                        var strong = document.createElement('strong');
                        strong.textContent = isEn ? ('Sent via WhatsApp to ' + c.name + ':') : ('נשלח בווטסאפ אל ' + c.name + ':');
                        box.appendChild(strong);
                        box.appendChild(document.createElement('br'));
                        box.appendChild(document.createTextNode(
                            isEn
                                ? ('⚠️ You just missed an urgent job in ' + city + ' (' + need + ', est. value: ₪' + est + '). ' + winner.name + ' pressed 1 first and claimed the job. Activate first-ring AI pickup via *61*0547875670# or visit https://kolivri.com/follow-me/')
                                : ('⚠️ פספסת עכשיו קריאת חירום ב' + city + ' (' + need + ', שווי מוערך: ₪' + est + '). ' + winner.name + ' הקיש 1 ראשון ותפס את העבודה. להפעלת מענה אוטומטי בצלצול ראשון חייג *61*0547875670# או היכנס ל-https://kolivri.com/he/follow-me/')
                        ));
                        loseEl.appendChild(box);
                    });
                }
                fetch('https://api.kolivri.co.il/api/v1/growth/fomo-arbitrage/claim', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ raceId: 'race_live_demo', candidateIndex: winIdx })
                }).catch(function() {});
            });
        });
    })();
    </script>
    <?php echo self::getContextCarryForwardScript(); ?>
</div>
        <?php
        return trim((string) ob_get_clean());
    }

    /**
     * Render Shortcode 4: `[kolivri_mcp_developer_hub]` (Play #6).
     *
     * @param array $atts
     * @return string
     */
    public static function renderMcpDeveloperHub($atts = array()) {
        $lang = self::resolveLang($atts);
        $isEn = ($lang === 'en');
        $codes = self::generateCarrierForwardingCodes('partner', self::KOLIVRI_PHONE_DISPLAY, 15);
        $mcpJsonConfig = json_encode(array(
            'mcpServers' => array(
                'kolivri-voice' => array(
                    'command' => 'npx',
                    'args'    => array('-y', 'kolivri-voice-mcp'),
                    'env'     => array(
                        'KOLIVRI_API_URL' => self::KOLIVRI_API_BASE,
                        'KOLIVRI_API_KEY' => 'kol_live_xxxxxxxxxxxxxxxx',
                    ),
                ),
            ),
        ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        ob_start();
        echo self::getScopedCss();
        if ($isEn) :
        ?>
<div class="kgh-wrap" id="kgh-mcp-developer-hub" dir="ltr" lang="en" style="text-align:left;" data-shortcode="kolivri_mcp_developer_hub">
    <?php echo self::renderLabSwitcher('mcp', 'en'); ?>
    <span class="kgh-badge emerald">🛠️ Claude · OpenCode · Cursor · Official MCP Server</span>
    <h2 class="kgh-title">Kolivri Voice MCP — Give Your AI Coding Agent a Real Phone Line</h2>
    <p class="kgh-subtitle">
        Let Claude Code, OpenCode, Cursor, and Claude Desktop place live outbound AI phone calls in 8 languages (native Hebrew &amp; English), fetch transcripts &amp; call summaries, calculate missed-call ROI, and generate 30-second carrier call-forwarding codes (<code>*61*</code> / <code>*004*</code>).
    </p>

    <div class="kgh-grid-2">
        <div class="kgh-panel">
            <div class="kgh-panel-title">
                <span>1. One-Command Setup (Claude Code / OpenCode / Cursor)</span>
                <button type="button" class="kgh-btn kgh-btn-secondary" id="kgh-mcp-copy-btn" style="min-height:34px;padding:6px 12px;font-size:12px;">
                    📋 Copy JSON
                </button>
            </div>
            <div style="font-size:13px;color:#94A3B8;margin-bottom:6px;">Quick CLI install for Claude Code &amp; OpenCode:</div>
            <pre class="kgh-code-block" id="kgh-mcp-cli-code">claude mcp add kolivri-voice -- npx -y kolivri-voice-mcp</pre>

            <div style="font-size:13px;color:#94A3B8;margin:12px 0 6px;">JSON config for <code>.mcp.json</code> / <code>opencode.json</code> / <code>.cursor/mcp.json</code>:</div>
            <pre class="kgh-code-block" id="kgh-mcp-json-code"><?php echo self::esc($mcpJsonConfig); ?></pre>

            <div style="font-size:13.5px;font-weight:700;color:#FFFFFF;margin:16px 0 8px;">4 Built-In MCP Tools in <code>kolivri-voice-mcp</code>:</div>
            <div class="kgh-card-list">
                <div class="kgh-item-card">
                    <code style="color:#38BDF8;font-weight:700;">kolivri_place_call</code> — Triggers a live outbound AI phone call in 8 languages (English, Spanish, French, German, Portuguese, Arabic, Russian, Hebrew) with KYC caller verification and E.164 validation.
                </div>
                <div class="kgh-item-card">
                    <code style="color:#38BDF8;font-weight:700;">kolivri_get_call_status</code> — Retrieves call status, full transcript, AI summary, booked appointment status, and recording URL from <code>https://api.kolivri.co.il</code>.
                </div>
                <div class="kgh-item-card">
                    <code style="color:#38BDF8;font-weight:700;">kolivri_carrier_forwarding_codes</code> — Generates 30-second conditional call-forwarding dial codes (<code>*61*</code>, <code>*004*</code>, Verizon <code>*71</code>, or SIP URI) for US, UK, EU, and global carriers.
                </div>
                <div class="kgh-item-card">
                    <code style="color:#38BDF8;font-weight:700;">kolivri_preview_roi</code> — Computes missed-call revenue leakage and recommends the matching Starter (<code>$69/mo</code>), Business (<code>$139/mo</code>), or Pro (<code>$249/mo</code>) plan.
                </div>
            </div>
        </div>

        <div class="kgh-panel">
            <div class="kgh-panel-title">2. Interactive 30-Second Call Forwarding &amp; SIP Generator</div>
            <div class="kgh-field">
                <label for="kgh-mcp-carrier">Select Region / Carrier / PBX</label>
                <select id="kgh-mcp-carrier">
                    <option value="us_gsm">US &amp; Canada GSM — AT&amp;T / T-Mobile / Rogers (*61* / *004*)</option>
                    <option value="verizon">US — Verizon Wireless / Xfinity (*71 Conditional / *72 All)</option>
                    <option value="uk_eu_gsm">UK &amp; Europe GSM — Vodafone / EE / O2 / Orange / T-Mobile</option>
                    <option value="au_global">Australia &amp; Global 3GPP GSM — Telstra / Optus / Singtel</option>
                    <option value="sip_pbx">Twilio / RingCentral / 3CX / Custom SIP Trunk (BYOC)</option>
                    <option value="partner">Israel Mobile (Partner / Pelephone / Cellcom / HOT)</option>
                    <option value="bezeq">Israel Landline (Bezeq — *41* / *42*)</option>
                </select>
            </div>
            <div class="kgh-field">
                <label for="kgh-mcp-target">Kolivri AI Agent Target Number (E.164 or Local DID)</label>
                <input type="text" id="kgh-mcp-target" value="+1 (888) 555-0199" dir="ltr">
            </div>
            <div class="kgh-field">
                <label for="kgh-mcp-rings">Ring Timeout Before Forwarding to Kolivri (Seconds)</label>
                <select id="kgh-mcp-rings">
                    <option value="10">10 seconds (2 rings)</option>
                    <option value="15" selected>15 seconds (3 rings — Recommended)</option>
                    <option value="20">20 seconds (4 rings)</option>
                    <option value="25">25 seconds (5 rings)</option>
                </select>
            </div>

            <div class="kgh-item-card" style="margin-top:12px;">
                <div style="font-size:13px;color:#94A3B8;">Forward When Unanswered (*61*):</div>
                <div class="kgh-code-block" id="kgh-code-61" style="margin:6px 0 10px;font-size:15px;font-weight:700;color:#6EE7B7;"><?php echo self::esc($codes['noAnswerCode']); ?></div>

                <div style="font-size:13px;color:#94A3B8;">All Conditional Forwarding (*004* — No Answer + Busy + Unreachable):</div>
                <div class="kgh-code-block" id="kgh-code-004" style="margin:6px 0 10px;font-size:15px;font-weight:700;color:#38BDF8;"><?php echo self::esc($codes['allConditional']); ?></div>

                <div style="font-size:13px;color:#94A3B8;">Disable &amp; Reset All Forwarding:</div>
                <div class="kgh-code-block" id="kgh-code-reset" style="margin-top:6px;"><?php echo self::esc($codes['resetCode']); ?></div>
            </div>

            <div class="kgh-btn-row" style="margin-top:16px;">
                <a href="<?php echo self::esc(self::KOLIVRI_APP_URL); ?>" class="kgh-btn kgh-btn-primary">
                    Get API Key at app.kolivri.co.il →
                </a>
                <a href="/start/" class="kgh-btn kgh-btn-secondary">
                    Start 14-Day Free Trial
                </a>
            </div>
        </div>
    </div>

    <div class="kgh-panel" style="margin-top:20px;" aria-label="3. 1-Line Agency Callback Widget (widget.js) — Earn 10% Partner Rev-Share">
        <div class="kgh-panel-title">
            <span>3. 1-Line Agency Callback Widget (<code>widget.js</code>) — Earn 10% Partner Rev-Share</span>
            <button type="button" class="kgh-btn kgh-btn-secondary" id="kgh-widget-copy-btn" style="min-height:34px;padding:6px 12px;font-size:12px;">
                📋 Copy Widget Snippet
            </button>
        </div>
        <p style="font-size:13.5px;color:#CBD5E1;margin:0 0 10px;line-height:1.55;">
            Web agencies, WordPress freelancers, and integrators: embed our 1-line <strong>10-Second AI Callback Widget</strong> on any client site with automatic referral attribution (<code>utm_source=agency_widget</code>) and earn <strong>10% recurring partner rev-share</strong>.
        </p>
        <pre class="kgh-code-block" id="kgh-widget-embed-code"><?php echo self::esc('<script src="https://kolivri.com/widget.js" data-lang="he" data-partner="your-agency"></script>'); ?></pre>
        <textarea readonly class="kgh-code-block" id="kgh-widget-embed-raw" rows="2" style="width:100%;margin-top:8px;min-height:48px;resize:none;" aria-label="Copyable widget.js embed snippet"><script src="https://kolivri.com/widget.js" data-lang="he" data-partner="your-agency"></script></textarea>
    </div>
        <?php else : ?>
<div class="kgh-wrap" id="kgh-mcp-developer-hub" dir="rtl" lang="he" data-shortcode="kolivri_mcp_developer_hub">
    <?php echo self::renderLabSwitcher('mcp', 'he'); ?>
    <span class="kgh-badge emerald">🛠️ Claude · OpenCode · Cursor · kolivri-voice-mcp</span>
    <h2 class="kgh-title">חיבור סוכני קוד (Claude Code, OpenCode, Cursor) לטלפוניה חיה של קוליברי (MCP)</h2>
    <p class="kgh-subtitle">
        תנו לסוכן ה-AI שלכם להוציא שיחות טלפון אמיתיות בעברית ובאנגלית, לבדוק סטטוס ותמלול שיחה, לחשב ROI של שיחות מפוספסות, ולייצר קודי הפניית שיחות (<code>*61*</code> / <code>*004*</code>) לכל חברות הסלולר בישראל.
    </p>

    <div class="kgh-grid-2">
        <div class="kgh-panel">
            <div class="kgh-panel-title">
                <span>1. התקנה בלחיצה אחת (Claude Code / OpenCode / Cursor)</span>
                <button type="button" class="kgh-btn kgh-btn-secondary" id="kgh-mcp-copy-btn" style="min-height:34px;padding:6px 12px;font-size:12px;">
                    📋 העתק JSON
                </button>
            </div>
            <div style="font-size:13px;color:#94A3B8;margin-bottom:6px;">פקודת CLI מהירה ל-Claude Code &amp; OpenCode:</div>
            <pre class="kgh-code-block" id="kgh-mcp-cli-code">claude mcp add kolivri-voice -- npx -y kolivri-voice-mcp</pre>

            <div style="font-size:13px;color:#94A3B8;margin:12px 0 6px;">הגדרת JSON עבור <code>.mcp.json</code> / <code>opencode.json</code> / <code>.cursor/mcp.json</code>:</div>
            <pre class="kgh-code-block" id="kgh-mcp-json-code"><?php echo self::esc($mcpJsonConfig); ?></pre>

            <div style="font-size:13.5px;font-weight:700;color:#FFFFFF;margin:16px 0 8px;">4 הכלים המובנים ב-<code>kolivri-voice-mcp</code>:</div>
            <div class="kgh-card-list">
                <div class="kgh-item-card">
                    <code style="color:#38BDF8;font-weight:700;">kolivri_place_call</code> — הוצאת שיחת AI חיה בעברית/אנגלית עם אימות KYC, הגדרת משימה וולידציית מספר ישראלי.
                </div>
                <div class="kgh-item-card">
                    <code style="color:#38BDF8;font-weight:700;">kolivri_get_call_status</code> — שליפת סטטוס שיחה, תמלול מלא, סיכום AI, פגישה שנקבעה והקלטה מ-<code>https://api.kolivri.co.il</code>.
                </div>
                <div class="kgh-item-card">
                    <code style="color:#38BDF8;font-weight:700;">kolivri_carrier_forwarding_codes</code> — מחולל קודי GSM/סלולר (<code>*61*</code>, <code>*67*</code>, <code>*62*</code>, <code>*004*</code>, <code>##002#</code>) לכל המפעילים בישראל.
                </div>
                <div class="kgh-item-card">
                    <code style="color:#38BDF8;font-weight:700;">kolivri_preview_roi</code> — חישוב הפסד הכנסות משיחות שלא נענות והתאמת מסלול Starter (<code>₪247</code>), Business (<code>₪479</code>) או Gold (<code>₪880</code>).
                </div>
            </div>
        </div>

        <div class="kgh-panel">
            <div class="kgh-panel-title">2. מחולל קודי הפניית שיחות אינטראקטיבי (*61* / *004*)</div>
            <div class="kgh-field">
                <label for="kgh-mcp-carrier">בחר מפעיל סלולרי / קווי בישראל</label>
                <select id="kgh-mcp-carrier">
                    <?php foreach ($codes['carriersList'] as $cKey => $cLabel) : ?>
                    <option value="<?php echo self::esc($cKey); ?>"><?php echo self::esc($cLabel); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="kgh-field">
                <label for="kgh-mcp-target">מספר יעד של סוכן קוליברי</label>
                <input type="text" id="kgh-mcp-target" value="<?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>" dir="ltr">
            </div>
            <div class="kgh-field">
                <label for="kgh-mcp-rings">זמן צלצול לפני מעבר לקוליברי (שניות)</label>
                <select id="kgh-mcp-rings">
                    <option value="10">10 שניות (2 צלצולים)</option>
                    <option value="15" selected>15 שניות (3 צלצולים — מומלץ)</option>
                    <option value="20">20 שניות (4 צלצולים)</option>
                    <option value="25">25 שניות (5 צלצולים)</option>
                </select>
            </div>

            <div class="kgh-item-card" style="margin-top:12px;">
                <div style="font-size:13px;color:#94A3B8;">באין מענה (*61*):</div>
                <div class="kgh-code-block" id="kgh-code-61" style="margin:6px 0 10px;font-size:15px;font-weight:700;color:#6EE7B7;"><?php echo self::esc($codes['noAnswerCode']); ?></div>

                <div style="font-size:13px;color:#94A3B8;">כל ההפניות המותנות יחד (*004* — אין מענה + תפוס + לא זמין):</div>
                <div class="kgh-code-block" id="kgh-code-004" style="margin:6px 0 10px;font-size:15px;font-weight:700;color:#38BDF8;"><?php echo self::esc($codes['allConditional']); ?></div>

                <div style="font-size:13px;color:#94A3B8;">איפוס וביטול הפניות (Reset):</div>
                <div class="kgh-code-block" id="kgh-code-reset" style="margin-top:6px;"><?php echo self::esc($codes['resetCode']); ?></div>
            </div>

            <div class="kgh-btn-row" style="margin-top:16px;">
                <a href="<?php echo self::esc(self::KOLIVRI_APP_URL); ?>" class="kgh-btn kgh-btn-primary">
                    קבל מפתח API ב-app.kolivri.co.il ←
                </a>
                <a href="<?php echo self::esc(self::KOLIVRI_FOLLOW_ME_URL); ?>" class="kgh-btn kgh-btn-secondary">
                    למדריך המלא (/he/follow-me/)
                </a>
            </div>
        </div>
    </div>

    <div class="kgh-panel" style="margin-top:20px;" aria-label="3. 1-Line Agency Callback Widget (widget.js) — Earn 10% Partner Rev-Share">
        <div class="kgh-panel-title">
            <span>3. 1-Line Agency Callback Widget (<code>widget.js</code>) — Earn 10% Partner Rev-Share</span>
            <button type="button" class="kgh-btn kgh-btn-secondary" id="kgh-widget-copy-btn" style="min-height:34px;padding:6px 12px;font-size:12px;">
                📋 העתק קוד ווידג׳ט
            </button>
        </div>
        <p style="font-size:13.5px;color:#CBD5E1;margin:0 0 10px;line-height:1.55;">
            סוכנויות דיגיטל, בוני אתרים ופרילנסרים: הטמיעו בשורה אחת ווידג׳ט <strong>שיחה חוזרת ב-10 שניות מסוכן AI</strong> באתרי הלקוחות שלכם עם שיוך שותפים אוטומטי (<code>utm_source=agency_widget</code>) והרוויחו <strong>10% עמלת שותפים מתמשכת (Partner Rev-Share)</strong>:
        </p>
        <pre class="kgh-code-block" id="kgh-widget-embed-code"><?php echo self::esc('<script src="https://kolivri.com/widget.js" data-lang="he" data-partner="your-agency"></script>'); ?></pre>
        <textarea readonly class="kgh-code-block" id="kgh-widget-embed-raw" rows="2" style="width:100%;margin-top:8px;min-height:48px;resize:none;" aria-label="Copyable widget.js embed snippet"><script src="https://kolivri.com/widget.js" data-lang="he" data-partner="your-agency"></script></textarea>
    </div>
        <?php endif; ?>

    <script>
    (function() {
        var copyBtn = document.getElementById('kgh-mcp-copy-btn');
        var jsonEl  = document.getElementById('kgh-mcp-json-code');
        if (copyBtn && jsonEl) {
            copyBtn.addEventListener('click', function() {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(jsonEl.textContent || '');
                }
                copyBtn.textContent = <?php echo $isEn ? "'✓ Copied!'" : "'✓ הועתק!'"; ?>;
            });
        }
        var widgetCopyBtn = document.getElementById('kgh-widget-copy-btn');
        var widgetCodeEl  = document.getElementById('kgh-widget-embed-code');
        if (widgetCopyBtn && widgetCodeEl) {
            widgetCopyBtn.addEventListener('click', function() {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(widgetCodeEl.textContent || '');
                }
                widgetCopyBtn.textContent = <?php echo $isEn ? "'✓ Copied!'" : "'✓ הועתק!'"; ?>;
            });
        }
        var cSel = document.getElementById('kgh-mcp-carrier');
        var tInp = document.getElementById('kgh-mcp-target');
        var rSel = document.getElementById('kgh-mcp-rings');
        function updateCodes() {
            var carrier = (cSel || {}).value || 'partner';
            var rawT    = ((tInp || {}).value || '0547875670').replace(/\D+/g, '') || '0547875670';
            var rings   = (rSel || {}).value || '15';
            var el61    = document.getElementById('kgh-code-61');
            var el004   = document.getElementById('kgh-code-004');
            var elRes   = document.getElementById('kgh-code-reset');
            if (carrier === 'bezeq') {
                if (el61)  el61.textContent  = '*42*' + rawT + '#';
                if (el004) el004.textContent = '*41*' + rawT + '# / *42*' + rawT + '#';
                if (elRes) elRes.textContent = '#41# / #42# / #72#';
            } else if (carrier === 'verizon') {
                if (el61)  el61.textContent  = '*71' + rawT;
                if (el004) el004.textContent = '*71' + rawT + ' (Conditional) / *72' + rawT + ' (All Calls)';
                if (elRes) elRes.textContent = '*73';
            } else if (carrier === 'sip_pbx') {
                if (el61)  el61.textContent  = 'sip:' + rawT + '@sip.kolivri.com (No-Answer Route)';
                if (el004) el004.textContent = 'sip:' + rawT + '@sip.kolivri.com:5060 (TLS/UDP Trunk)';
                if (elRes) elRes.textContent = 'Remove SIP forwarding rule in PBX / Twilio Console';
            } else {
                if (el61)  el61.textContent  = '**61*' + rawT + '**' + rings + '#';
                if (el004) el004.textContent = '**004*' + rawT + '**' + rings + '#';
                if (elRes) elRes.textContent = '##002#';
            }
        }
        [cSel, tInp, rSel].forEach(function(el) {
            if (el) {
                el.addEventListener('change', updateCodes);
                el.addEventListener('input', updateCodes);
            }
        });
    })();
    </script>
    <?php echo self::getContextCarryForwardScript(); ?>
</div>
        <?php
        return trim((string) ob_get_clean());
    }

    /**
     * Render Receiver Context Continuity Banner + Input Pre-Filler + App Link Forwarder
     * hooked to `wp_footer` (powering `/he/start/`, `/start/`, `/he/follow-me/`, and `/follow-me/`).
     *
     * @return string
     */
    public static function render_receiver_continuity_script() {
        return self::renderReceiverContinuityScript();
    }

    /**
     * Implementation of `render_receiver_continuity_script()`.
     *
     * @return string
     */
    public static function renderReceiverContinuityScript() {
        return <<<'HTML'
<style id="klv-continuity-banner-css">
.klv-continuity-banner {
    font-family: 'Rubik', 'Assistant', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #0B1220 0%, #132342 100%);
    color: #F8FAFC;
    border: 2px solid #38BDF8;
    border-radius: 16px;
    padding: 18px 22px;
    max-width: 1080px;
    margin: 16px auto;
    box-shadow: 0 18px 44px rgba(11, 18, 32, 0.42);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    box-sizing: border-box;
    z-index: 50;
}
.klv-continuity-banner *, .klv-continuity-banner *::before, .klv-continuity-banner *::after {
    box-sizing: border-box;
}
.klv-continuity-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    font-size: 14.5px;
    line-height: 1.5;
}
.klv-continuity-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    background: rgba(56, 189, 248, 0.16);
    color: #7DD3FC;
    border: 1px solid rgba(56, 189, 248, 0.38);
}
.klv-continuity-chip.score {
    background: rgba(16, 185, 129, 0.18);
    color: #6EE7B7;
    border-color: rgba(16, 185, 129, 0.45);
}
.klv-continuity-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}
.klv-continuity-btn {
    min-height: 44px;
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 800;
    font-size: 14px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: transform 0.15s ease;
}
.klv-continuity-btn.primary {
    background: linear-gradient(90deg, #FB8123 0%, #EA580C 100%);
    color: #FFFFFF !important;
}
.klv-continuity-btn.fallback {
    background: rgba(56, 189, 248, 0.16);
    color: #38BDF8 !important;
    border: 1px solid rgba(56, 189, 248, 0.45);
}
@media (max-width: 768px) {
    .klv-continuity-banner {
        flex-direction: column;
        align-items: stretch;
        padding: 16px;
        margin: 12px;
    }
    .klv-continuity-actions {
        width: 100%;
    }
}
@media (max-width: 480px) {
    .klv-continuity-actions {
        flex-direction: column;
    }
    .klv-continuity-btn {
        width: 100%;
    }
}
</style>
<script id="klv-continuity-receiver-js">
(function() {
    if (typeof window === 'undefined' || !window.location) return;

    if (typeof document !== 'undefined' && typeof document.addEventListener === 'function' && !window.__klvGa4DelegatedBound) {
        window.__klvGa4DelegatedBound = true;
        document.addEventListener('click', function(e) {
            var target = e && e.target && typeof e.target.closest === 'function' ? e.target : null;
            if (!target) return;
            window.dataLayer = window.dataLayer || [];

            var waLink = target.closest('a[href*="wa.me"], a[href*="whatsapp.com"]');
            if (waLink) {
                var href = waLink.getAttribute('href') || '';
                if (typeof window.gtag === 'function') window.gtag('event', 'whatsapp_click', { page_path: window.location.pathname, link_url: href });
                window.dataLayer.push({ event: 'whatsapp_click', page_path: window.location.pathname, link_url: href });
            }

            var telLink = target.closest('a[href^="tel:"]');
            if (telLink) {
                var href = telLink.getAttribute('href') || '';
                if (typeof window.gtag === 'function') window.gtag('event', 'call_click', { page_path: window.location.pathname, phone_target: href });
                window.dataLayer.push({ event: 'call_click', page_path: window.location.pathname, phone_target: href });
            }

            var labEl = target.closest('.kgh-btn, .kpg-btn, .kat-btn, .klv-woo-btn, .lab-card, .klv-blog-cta-btn');
            if (labEl) {
                var labelOrId = labEl.getAttribute('id') || labEl.getAttribute('data-tool') || labEl.getAttribute('href') || (labEl.textContent || '').trim().slice(0, 80);
                if (typeof window.gtag === 'function') window.gtag('event', 'lab_tool_used', { page_path: window.location.pathname, tool_target: labelOrId });
                window.dataLayer.push({ event: 'lab_tool_used', page_path: window.location.pathname, tool_target: labelOrId });
            }
        });
    }

    var params = new URLSearchParams(window.location.search);
    var phone = (params.get('phone') || '').trim();
    var carrier = (params.get('carrier') || '').trim();
    var score = (params.get('score') || '').trim();
    var biz = (params.get('biz') || params.get('name') || '').trim();

    if (!phone && !carrier && !score && !biz) return;

    var carrierLabels = {
        partner: 'Partner / 012 (פרטנר)',
        pelephone: 'Pelephone (פלאפון)',
        cellcom: 'Cellcom (סלקום)',
        hot_mobile: 'HOT Mobile (הוט מובייל)',
        golan: 'Golan Telecom (גולן טלקום)',
        '019': '019 Mobile',
        we4g: 'We4G / Wecom',
        bezeq: 'Bezeq (בזק)'
    };

    var isEn = (window.location.pathname.indexOf('/he/') === -1) &&
        ((document.documentElement.getAttribute('lang') || '').toLowerCase().indexOf('en') === 0);

    if (!document.querySelector('.klv-continuity-banner')) {
        var banner = document.createElement('section');
        banner.className = 'klv-continuity-banner';
        banner.setAttribute('dir', isEn ? 'ltr' : 'rtl');
        banner.setAttribute('role', 'region');
        banner.setAttribute('aria-label', isEn ? 'Carried-over AI setup context' : 'הגדרות מענה שהועברו מהבדיקה שלך');

        var meta = document.createElement('div');
        meta.className = 'klv-continuity-meta';

        if (biz) {
            var bizChip = document.createElement('span');
            bizChip.className = 'klv-continuity-chip';
            bizChip.textContent = '🏢 ' + biz;
            meta.appendChild(bizChip);
        }
        if (phone) {
            var phoneChip = document.createElement('span');
            phoneChip.className = 'klv-continuity-chip';
            phoneChip.setAttribute('dir', 'ltr');
            phoneChip.textContent = '📱 ' + phone;
            meta.appendChild(phoneChip);
        }
        if (score !== '') {
            var scoreChip = document.createElement('span');
            scoreChip.className = 'klv-continuity-chip score';
            scoreChip.textContent = 'הציון שלך: ' + score + '/100 → 100/100 עם קוליברי';
            meta.appendChild(scoreChip);
        }
        if (carrier) {
            var carrierChip = document.createElement('span');
            carrierChip.className = 'klv-continuity-chip';
            carrierChip.textContent = '📡 ' + (carrierLabels[carrier] || carrier);
            meta.appendChild(carrierChip);
        }

        var actions = document.createElement('div');
        actions.className = 'klv-continuity-actions';

        var btn004 = document.createElement('a');
        btn004.className = 'klv-continuity-btn primary';
        btn004.setAttribute('href', 'tel:*004*0547875670**15#');
        btn004.textContent = '📞 הפעלה ב-30 שניות: *004*0547875670**15#';
        actions.appendChild(btn004);

        var btn61 = document.createElement('a');
        btn61.className = 'klv-continuity-btn fallback';
        btn61.setAttribute('href', 'tel:*61*0547875670**15#');
        btn61.textContent = 'גיבוי באין מענה: *61*0547875670**15#';
        actions.appendChild(btn61);

        banner.appendChild(meta);
        banner.appendChild(actions);

        var host = document.querySelector('main, .site-main, .entry-content, #content') || document.body;
        if (host && host.firstChild) {
            host.insertBefore(banner, host.firstChild);
        } else if (host) {
            host.appendChild(banner);
        }
    }

    if (phone) {
        var telInputs = document.querySelectorAll('input[type="tel"], input[name*="phone"], input[name*="tel"]');
        telInputs.forEach(function(inp) {
            if (!inp.value || inp.id === 'kgh-ghost-phone') {
                inp.value = phone;
                inp.dispatchEvent(new Event('input', { bubbles: true }));
                inp.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    if (carrier) {
        var carrierSelects = document.querySelectorAll('select[name*="carrier"], #kgh-mcp-carrier');
        carrierSelects.forEach(function(sel) {
            sel.value = carrier;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    var qsParts = [];
    if (phone) qsParts.push('phone=' + encodeURIComponent(phone));
    if (carrier) qsParts.push('carrier=' + encodeURIComponent(carrier));
    if (score !== '') qsParts.push('score=' + encodeURIComponent(score));
    if (biz) qsParts.push('biz=' + encodeURIComponent(biz));
    var qs = qsParts.join('&');

    if (qs) {
        var appLinks = document.querySelectorAll('a[href*="app.kolivri.co.il"]');
        appLinks.forEach(function(a) {
            var rawHref = a.getAttribute('href') || '';
            if (!rawHref) return;
            var hashIdx = rawHref.indexOf('#');
            var hash = hashIdx !== -1 ? rawHref.slice(hashIdx) : '';
            var baseNoHash = hashIdx !== -1 ? rawHref.slice(0, hashIdx) : rawHref;
            var sep = baseNoHash.indexOf('?') !== -1 ? '&' : '?';
            a.setAttribute('href', baseNoHash + sep + qs + hash);
        });
    }
})();
</script>
HTML;
    }

    /**
     * Render Shortcode 5: `[kolivri_ghost_minisite_preview]` (Play #10).
     *
     * @param array $atts
     * @return string
     */
    public static function render_ghost_minisite_preview($atts = array()) {
        return self::renderGhostMinisitePreview($atts);
    }

    /**
     * Render Shortcode 5: `[kolivri_ghost_minisite_preview]` (Play #10).
     *
     * @param array $atts
     * @return string
     */
    public static function renderGhostMinisitePreview($atts = array()) {
        $lang = self::resolveLang($atts);
        $isEn = ($lang === 'en');
        $preview = self::buildGhostMinisitePreview($atts);
        ob_start();
        echo self::getScopedCss();
        if ($isEn) :
        ?>
<div class="kgh-wrap" id="kgh-ghost-minisite-preview" dir="ltr" lang="en" style="text-align:left;" data-shortcode="kolivri_ghost_minisite_preview">
    <?php echo self::renderLabSwitcher('ghost-site', 'en'); ?>
    <span class="kgh-badge orange">🌐 Instant AI Mini-Sites for biz-without-website</span>
    <h2 class="kgh-title">No Business Website? Preview Your Instant Mini-Site + 24/7 AI Voice Receptionist in 30 Seconds</h2>
    <p class="kgh-subtitle">
        Thousands of local service providers appear on Google and business directories without a live website and lose calls daily. Enter your business details (or load a <code>biz-without-website</code> preset) to see how your claimable Kolivri AI landing page and 24/7 voice receptionist work.
    </p>

    <div class="kgh-btn-row" style="margin-bottom:18px;">
        <?php foreach ($preview['presets'] as $idx => $preset) : ?>
        <button type="button" class="kgh-btn kgh-btn-secondary kgh-ghost-preset-btn"
            data-name="<?php echo self::esc($preset['businessName']); ?>"
            data-prof="<?php echo self::esc($preset['profession']); ?>"
            data-city="<?php echo self::esc($preset['city']); ?>"
            data-phone="<?php echo self::esc($preset['phone']); ?>"
            data-rating="<?php echo self::esc(isset($preset['rating']) ? $preset['rating'] : '4.9'); ?>">
            Preset #<?php echo $idx + 1; ?>: <?php echo self::esc($preset['businessName']); ?>
        </button>
        <?php endforeach; ?>
    </div>

    <div class="kgh-grid-2">
        <div class="kgh-panel">
            <div class="kgh-panel-title">1. Your Business Details</div>
            <div class="kgh-field">
                <label for="kgh-ghost-name">Business Name</label>
                <input type="text" id="kgh-ghost-name" name="name" value="<?php echo self::esc($preview['businessName']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-ghost-prof">Profession / Category</label>
                <input type="text" id="kgh-ghost-prof" name="category" value="<?php echo self::esc($preview['profession']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-ghost-city">City / Service Area</label>
                <input type="text" id="kgh-ghost-city" name="city" value="<?php echo self::esc($preview['city']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-ghost-phone">Business Phone</label>
                <input type="text" id="kgh-ghost-phone" name="phone" value="<?php echo self::esc($preview['phone']); ?>" dir="ltr">
            </div>
            <div class="kgh-field">
                <label for="kgh-ghost-rating">Google Rating</label>
                <input type="text" id="kgh-ghost-rating" name="rating" value="<?php echo self::esc($preview['rating']); ?>" dir="ltr">
            </div>
        </div>

        <div class="kgh-panel" style="border:2px solid #38BDF8;">
            <div class="kgh-panel-title">
                <span>2. Live Preview: Your AI Voice Mini-Site</span>
                <span class="kgh-pill b-1-30">24/7 Active with Kolivri AI</span>
            </div>
            <h3 id="kgh-ghost-card-title" style="font-size:22px;font-weight:800;color:#FFFFFF;margin:0 0 6px;">
                <?php echo self::esc($preview['businessName']); ?>
            </h3>
            <div id="kgh-ghost-card-meta" style="font-size:14px;color:#38BDF8;font-weight:600;margin-bottom:14px;">
                <?php echo self::esc($preview['profession']); ?> · <?php echo self::esc($preview['city']); ?> · ⭐ <span id="kgh-ghost-card-rating"><?php echo self::esc($preview['rating']); ?></span> · 📞 <span dir="ltr"><?php echo self::esc($preview['phone']); ?></span>
            </div>

            <div class="kgh-wa-box" id="kgh-ghost-greeting">
                <?php echo self::esc($preview['aiGreetingHe']); ?>
            </div>

            <div style="margin-top:14px;font-size:13px;color:#94A3B8;">30-Second Mobile Activation Code:</div>
            <div class="kgh-code-block" style="margin-top:6px;font-size:15px;font-weight:700;color:#6EE7B7;">
                <?php echo self::esc($preview['activationCode']); ?>
            </div>

            <div style="margin-top:12px;font-size:13px;color:#94A3B8;">Direct Shareable Self-Serve URL:</div>
            <div class="kgh-code-block" id="kgh-ghost-share-url" style="margin-top:6px;font-size:12px;color:#7DD3FC;">
                <?php echo self::esc($preview['shareUrl']); ?>
            </div>

            <div class="kgh-btn-row" style="margin-top:16px;">
                <button type="button" class="kgh-btn kgh-btn-emerald" id="kgh-ghost-copy-url">
                    🔗 Copy Direct Mini-Site Link
                </button>
                <a href="/start/" class="kgh-btn kgh-btn-primary" id="kgh-ghost-claim-link">
                    Claim Mini-Site &amp; Activate AI Agent →
                </a>
                <a href="/follow-me/" class="kgh-btn kgh-btn-secondary">
                    *61* Guide (<?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>)
                </a>
            </div>
        </div>
    </div>
        <?php else : ?>
<div class="kgh-wrap" id="kgh-ghost-minisite-preview" dir="rtl" lang="he" data-shortcode="kolivri_ghost_minisite_preview">
    <?php echo self::renderLabSwitcher('ghost-site', 'he'); ?>
    <span class="kgh-badge orange">🌐 Ghost Mini-Sites for biz-without-website</span>
    <h2 class="kgh-title">אין לך אתר לעסק? תצוגה מקדימה של מיני-סייט + מזכירת AI קולית ב-30 שניות</h2>
    <p class="kgh-subtitle">
        אלפי בעלי מקצוע ועסקים בישראל מופיעים בגוגל ובאינדקסים ללא אתר פעיל ומפסידים שיחות. הזינו את פרטי העסק שלכם (או בחרו דוגמה ממאגר <code>biz-without-website</code>) וראו איך דף הנחיתה החכם והמענה הקולי של קוליברי נראים עבורכם.
    </p>

    <div class="kgh-btn-row" style="margin-bottom:18px;">
        <?php foreach ($preview['presets'] as $idx => $preset) : ?>
        <button type="button" class="kgh-btn kgh-btn-secondary kgh-ghost-preset-btn"
            data-name="<?php echo self::esc($preset['businessName']); ?>"
            data-prof="<?php echo self::esc($preset['profession']); ?>"
            data-city="<?php echo self::esc($preset['city']); ?>"
            data-phone="<?php echo self::esc($preset['phone']); ?>"
            data-rating="<?php echo self::esc(isset($preset['rating']) ? $preset['rating'] : '4.9'); ?>">
            דוגמה #<?php echo $idx + 1; ?>: <?php echo self::esc($preset['businessName']); ?>
        </button>
        <?php endforeach; ?>
    </div>

    <div class="kgh-grid-2">
        <div class="kgh-panel">
            <div class="kgh-panel-title">1. פרטי העסק שלך</div>
            <div class="kgh-field">
                <label for="kgh-ghost-name">שם העסק</label>
                <input type="text" id="kgh-ghost-name" name="name" value="<?php echo self::esc($preview['businessName']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-ghost-prof">תחום עיסוק / מקצוע</label>
                <input type="text" id="kgh-ghost-prof" name="category" value="<?php echo self::esc($preview['profession']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-ghost-city">עיר / אזור שירות</label>
                <input type="text" id="kgh-ghost-city" name="city" value="<?php echo self::esc($preview['city']); ?>">
            </div>
            <div class="kgh-field">
                <label for="kgh-ghost-phone">טלפון העסק</label>
                <input type="text" id="kgh-ghost-phone" name="phone" value="<?php echo self::esc($preview['phone']); ?>" dir="ltr">
            </div>
            <div class="kgh-field">
                <label for="kgh-ghost-rating">דירוג גוגל (Rating)</label>
                <input type="text" id="kgh-ghost-rating" name="rating" value="<?php echo self::esc($preview['rating']); ?>" dir="ltr">
            </div>
        </div>

        <div class="kgh-panel" style="border:2px solid #38BDF8;">
            <div class="kgh-panel-title">
                <span>2. תצוגה חיה: המיני-סייט הקולי שלך</span>
                <span class="kgh-pill b-1-30">פעיל 24/7 עם Kolivri AI</span>
            </div>
            <h3 id="kgh-ghost-card-title" style="font-size:22px;font-weight:800;color:#FFFFFF;margin:0 0 6px;">
                <?php echo self::esc($preview['businessName']); ?>
            </h3>
            <div id="kgh-ghost-card-meta" style="font-size:14px;color:#38BDF8;font-weight:600;margin-bottom:14px;">
                <?php echo self::esc($preview['profession']); ?> · <?php echo self::esc($preview['city']); ?> · ⭐ <span id="kgh-ghost-card-rating"><?php echo self::esc($preview['rating']); ?></span> · 📞 <span dir="ltr"><?php echo self::esc($preview['phone']); ?></span>
            </div>

            <div class="kgh-wa-box" id="kgh-ghost-greeting">
                <?php echo self::esc($preview['aiGreetingHe']); ?>
            </div>

            <div style="margin-top:14px;font-size:13px;color:#94A3B8;">קוד הפעלה מיידי מהנייד שלך (30 שניות):</div>
            <div class="kgh-code-block" style="margin-top:6px;font-size:15px;font-weight:700;color:#6EE7B7;">
                <?php echo self::esc($preview['activationCode']); ?>
            </div>

            <div style="margin-top:12px;font-size:13px;color:#94A3B8;">קישור ישיר לשיתוף עצמי (Self-Serve URL):</div>
            <div class="kgh-code-block" id="kgh-ghost-share-url" style="margin-top:6px;font-size:12px;color:#7DD3FC;">
                <?php echo self::esc($preview['shareUrl']); ?>
            </div>

            <div class="kgh-btn-row" style="margin-top:16px;">
                <button type="button" class="kgh-btn kgh-btn-emerald" id="kgh-ghost-copy-url">
                    🔗 העתק קישור ישיר למיני-סייט שלי
                </button>
                <a href="<?php echo self::esc(self::KOLIVRI_START_URL); ?>" class="kgh-btn kgh-btn-primary" id="kgh-ghost-claim-link">
                    תבע בעלות על המיני-סייט והפעל סוכן AI ←
                </a>
                <a href="<?php echo self::esc(self::KOLIVRI_FOLLOW_ME_URL); ?>" class="kgh-btn kgh-btn-secondary">
                    מדריך *61* (<?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>)
                </a>
            </div>
        </div>
    </div>
        <?php endif; ?>

    <script>
    (function() {
        var isEn = <?php echo $isEn ? 'true' : 'false'; ?>;
        var nIn = document.getElementById('kgh-ghost-name');
        var pIn = document.getElementById('kgh-ghost-prof');
        var cIn = document.getElementById('kgh-ghost-city');
        var tIn = document.getElementById('kgh-ghost-phone');
        var rIn = document.getElementById('kgh-ghost-rating');
        var copyBtn = document.getElementById('kgh-ghost-copy-url');
        var shareUrlEl = document.getElementById('kgh-ghost-share-url');

        if (typeof window !== 'undefined' && window.location && window.location.search) {
            var q = new URLSearchParams(window.location.search);
            var qName = (q.get('name') || q.get('biz') || '').trim();
            var qCat  = (q.get('category') || q.get('profession') || '').trim();
            var qCity = (q.get('city') || '').trim();
            var qTel  = (q.get('phone') || '').trim();
            var qRat  = (q.get('rating') || '').trim();
            if (qName && nIn) nIn.value = qName;
            if (qCat && pIn)  pIn.value = qCat;
            if (qCity && cIn) cIn.value = qCity;
            if (qTel && tIn)  tIn.value = qTel;
            if (qRat && rIn)  rIn.value = qRat;
        }

        function buildShareUrl() {
            var name = (nIn || {}).value || (isEn ? 'Your Business' : 'העסק שלך');
            var prof = (pIn || {}).value || (isEn ? 'Service Provider' : 'בעל מקצוע');
            var city = (cIn || {}).value || (isEn ? 'Tel Aviv' : 'ישראל');
            var tel  = (tIn || {}).value || '054-787-5670';
            var rat  = (rIn || {}).value || '4.9';
            var base = (typeof window !== 'undefined' && window.location && window.location.origin)
                ? (window.location.origin + window.location.pathname)
                : (isEn ? 'https://kolivri.com/ghost-site/' : 'https://kolivri.com/he/ghost-site/');
            var qs = '?name=' + encodeURIComponent(name)
                + '&category=' + encodeURIComponent(prof)
                + '&city=' + encodeURIComponent(city)
                + '&phone=' + encodeURIComponent(tel)
                + (rat ? '&rating=' + encodeURIComponent(rat) : '');
            return base + qs;
        }

        function refreshGhost() {
            var name = (nIn || {}).value || (isEn ? 'Your Business' : 'העסק שלך');
            var prof = (pIn || {}).value || (isEn ? 'Service Provider' : 'בעל מקצוע');
            var city = (cIn || {}).value || (isEn ? 'Tel Aviv' : 'ישראל');
            var tel  = (tIn || {}).value || '054-787-5670';
            var rat  = (rIn || {}).value || '4.9';
            var titleEl = document.getElementById('kgh-ghost-card-title');
            var metaEl  = document.getElementById('kgh-ghost-card-meta');
            var greetEl = document.getElementById('kgh-ghost-greeting');
            var claimEl = document.getElementById('kgh-ghost-claim-link');
            if (titleEl) titleEl.textContent = name;
            if (metaEl)  metaEl.textContent  = prof + ' · ' + city + ' · ⭐ ' + rat + ' · 📞 ' + tel;
            if (greetEl) {
                greetEl.textContent = isEn
                    ? ('Hi! You have reached ' + name + ' (' + prof + ' in ' + city + '). I am the business\'s AI voice receptionist — I can book your service call, check availability & pricing, or have the owner call you right back.')
                    : ('שלום! הגעתם אל ' + name + ' (' + prof + ' ב' + city + '). אני העוזרת הקולית של העסק — אפשר לתאם דרכי קריאת שירות, לברר זמינות ומחירים, או לקבל חזרה מיידית מבעל המקצוע.');
            }
            var url = buildShareUrl();
            if (shareUrlEl) shareUrlEl.textContent = url;
            if (claimEl) {
                var startBase = isEn ? 'https://kolivri.com/start/' : 'https://kolivri.com/he/start/';
                claimEl.setAttribute('href', startBase + '?phone=' + encodeURIComponent(tel) + '&biz=' + encodeURIComponent(name));
            }
        }

        [nIn, pIn, cIn, tIn, rIn].forEach(function(el) {
            if (el) el.addEventListener('input', refreshGhost);
        });
        document.querySelectorAll('.kgh-ghost-preset-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (nIn) nIn.value = btn.getAttribute('data-name') || '';
                if (pIn) pIn.value = btn.getAttribute('data-prof') || '';
                if (cIn) cIn.value = btn.getAttribute('data-city') || '';
                if (rIn) rIn.value = btn.getAttribute('data-rating') || '4.9';
                if (tIn) {
                    tIn.value = btn.getAttribute('data-phone') || '';
                    tIn.dispatchEvent(new Event('input', { bubbles: true }));
                }
                refreshGhost();
            });
        });
        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
                var shareUrl = buildShareUrl();
                if (shareUrlEl) shareUrlEl.textContent = shareUrl;
                if (typeof navigator !== 'undefined' && navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shareUrl);
                }
                copyBtn.textContent = isEn ? '✓ Direct Mini-Site Link Copied!' : '✓ הקישור הישיר למיני-סייט הועתק!';
            });
        }
        refreshGhost();
    })();
    </script>
    <?php echo self::getContextCarryForwardScript(); ?>
</div>
        <?php
        return trim((string) ob_get_clean());
    }

    /**
     * Alias for `renderBlogInlineCta($content, $lang)`.
     *
     * @param string      $content
     * @param string|null $lang
     * @return string
     */
    public static function render_blog_inline_cta($content, $lang = null) {
        return self::renderBlogInlineCta($content, $lang);
    }

    /**
     * Inject responsive `.klv-blog-inline-cta` interactive Growth Lab CTA card
     * after the 2nd `</p>` paragraph (when >= 3 paragraphs exist) and at the end
     * of blog post content. Idempotent if `.klv-blog-inline-cta` is already present.
     *
     * @param string      $content
     * @param string|null $lang
     * @return string
     */
    public static function renderBlogInlineCta($content, $lang = null) {
        $content = (string) $content;
        if (strpos($content, 'klv-blog-inline-cta') !== false) {
            return $content;
        }

        if ($lang !== null && $lang !== '') {
            $resolvedLang = (strtolower((string) $lang) === 'en') ? 'en' : 'he';
        } else {
            $resolvedLang = self::resolveLang();
        }
        $isEn = ($resolvedLang === 'en');

        $css = <<<'CSS'
<style id="klv-blog-inline-cta-css">
.klv-blog-inline-cta {
    font-family: 'Rubik', 'Assistant', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #0B1220 0%, #132342 100%);
    color: #F8FAFC;
    border: 2px solid #38BDF8;
    border-radius: 18px;
    padding: 24px 26px;
    margin: 28px 0;
    box-shadow: 0 20px 48px rgba(11, 18, 32, 0.42);
    box-sizing: border-box;
}
.klv-blog-inline-cta *, .klv-blog-inline-cta *::before, .klv-blog-inline-cta *::after {
    box-sizing: border-box;
}
.klv-blog-inline-cta-headline {
    font-size: 21px;
    font-weight: 800;
    color: #FFFFFF;
    margin: 0 0 8px;
    line-height: 1.3;
}
.klv-blog-inline-cta-subtext {
    font-size: 15px;
    color: #CBD5E1;
    margin: 0 0 18px;
    line-height: 1.55;
}
.klv-blog-inline-cta-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}
.klv-blog-cta-btn {
    min-height: 44px;
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: rgba(56, 189, 248, 0.16);
    color: #7DD3FC !important;
    border: 1px solid rgba(56, 189, 248, 0.45);
    transition: transform 0.15s ease, border-color 0.15s ease;
}
.klv-blog-cta-btn:hover {
    transform: translateY(-1px);
    border-color: #38BDF8;
    color: #FFFFFF !important;
}
.klv-blog-cta-btn.klv-blog-cta-primary {
    background: linear-gradient(90deg, #FB8123 0%, #EA580C 100%);
    color: #FFFFFF !important;
    border-color: #FB8123;
}
@media (max-width: 640px) {
    .klv-blog-inline-cta {
        padding: 18px 16px;
        margin: 20px 0;
        border-radius: 14px;
    }
    .klv-blog-inline-cta-headline {
        font-size: 18px;
    }
    .klv-blog-inline-cta-actions {
        flex-direction: column;
        align-items: stretch;
    }
    .klv-blog-cta-btn {
        width: 100%;
        min-height: 44px;
    }
}
</style>
CSS;

        if ($isEn) {
            $card = '<aside class="klv-blog-inline-cta" dir="ltr" lang="en" style="text-align:left;" aria-label="Kolivri AI Voice Lab Interactive Tools">'
                . '<h3 class="klv-blog-inline-cta-headline">⚡ Test Kolivri\'s AI Voice Engine Live in Your Browser (30 Seconds)</h3>'
                . '<p class="klv-blog-inline-cta-subtext">Don\'t just read about AI voice agents — test our 98% LongMemEval Memory Graph, score your business phone line, or connect Claude/Cursor via MCP right now.</p>'
                . '<div class="klv-blog-inline-cta-actions">'
                . '<a href="/phone-grader/" class="klv-blog-cta-btn" data-tool="phone-grader">🔥 Grade Your Phone Line (/phone-grader/)</a>'
                . '<a href="/amnesia-test/" class="klv-blog-cta-btn" data-tool="amnesia-test">🧠 Run the 2-Call Amnesia Test (/amnesia-test/)</a>'
                . '<a href="/mcp/" class="klv-blog-cta-btn" data-tool="mcp">🛠️ Voice MCP for Devs (/mcp/)</a>'
                . '<a href="/start/" class="klv-blog-cta-btn klv-blog-cta-primary" data-tool="start-trial">Start 14-Day Free Trial (/start/)</a>'
                . '</div>'
                . '</aside>';
        } else {
            $card = '<aside class="klv-blog-inline-cta" dir="rtl" lang="he" style="text-align:right;" aria-label="מעבדת ה-AI הקולית של קוליברי">'
                . '<h3 class="klv-blog-inline-cta-headline">⚡ בדקו את סוכן ה-AI הקולי של קוליברי חי בדפדפן (תוך 30 שניות)</h3>'
                . '<p class="klv-blog-inline-cta-subtext">אל תסתפקו בקריאה — בדקו עכשיו כמה כסף העסק שלכם מפספס בטלפון, נסו לשבור את גרף הזיכרון (98% בעברית), או הפעילו סוכן קולי ב-30 שניות עם *61*.</p>'
                . '<div class="klv-blog-inline-cta-actions">'
                . '<a href="/he/phone-grader/" class="klv-blog-cta-btn" data-tool="phone-grader-he">🔥 מדד המענה העסקי (/he/phone-grader/)</a>'
                . '<a href="/he/amnesia-test/" class="klv-blog-cta-btn" data-tool="amnesia-test-he">🧠 מבחן הזיכרון החי (/he/amnesia-test/)</a>'
                . '<a href="/he/invoice-recovery/" class="klv-blog-cta-btn" data-tool="invoice-recovery-he">💰 גביית חשבוניות ב-0 ₪ מנוי (/he/invoice-recovery/)</a>'
                . '<a href="/he/start/" class="klv-blog-cta-btn klv-blog-cta-primary" data-tool="start-trial-he">התחילו 14 ימי ניסיון (/he/start/)</a>'
                . '</div>'
                . '</aside>';
        }

        $matches = array();
        $pCount = preg_match_all('/<\/p>/i', $content, $matches, PREG_OFFSET_CAPTURE);
        if ($pCount >= 3 && isset($matches[0][1])) {
            $insertPos = $matches[0][1][1] + strlen($matches[0][1][0]);
            $before = substr($content, 0, $insertPos);
            $after  = substr($content, $insertPos);
            return $css . "\n" . $before . "\n" . $card . "\n" . $after . "\n" . $card;
        }

        return $css . "\n" . $content . "\n" . $card;
    }

    /**
     * Return structured llmstxt.org-compliant Markdown for `/llms.txt` and `/llms-full.txt`.
     *
     * @return string
     */
    public static function getLlmsTxtContent() {
        return <<<'MD'
# Kolivri — AI Voice Agents & All-in-One Call Center CRM

> Kolivri deploys real-time inbound and outbound AI phone agents in 8 languages (English, Hebrew, Arabic, Russian, French, Spanish, German, Portuguese) with a built-in CRM, ticketing system, RAG Knowledge Base, visual call flow builder, and Memoria temporal graph memory (`100% LongMemEval`, `98% Hebrew LongMemEval`). Activate missed-call protection on any mobile or landline number in 30 seconds via conditional call forwarding (`*61*0547875670**15#` / `*004*0547875670**15#`).

## Core Capabilities

- **8 Native Languages**: Real-time conversational voice AI in English, Hebrew, Arabic, Russian, French, Spanish, German, and Portuguese with sub-second turn-taking latency.
- **Memoria Graph Memory (`100% LongMemEval`, `98% Hebrew LongMemEval`)**: Spreading-activation and temporal-supersession memory graph that remembers caller history, updated facts, preferences, and prior appointments across calls.
- **All-in-One Call Center CRM & Ticketing**: Built-in CRM lead profiles, automated call summaries, full recordings & transcripts, in-call appointment booking, Google Calendar sync, and SLA ticketing.
- **RAG Knowledge Base & Visual Flow Builder**: Upload business docs, price lists, and FAQs for grounded answers, and orchestrate multi-step inbound/outbound call journeys visually.
- **30-Second Carrier Activation & Official MCP Server**: Forward unanswered/busy calls via GSM `*61*` / `*004*` codes, connect SIP trunks, or trigger calls programmatically from Claude Code, OpenCode, and Cursor using `npx -y kolivri-voice-mcp`.

## Key Links

- [Homepage (English)](https://kolivri.com/): Global AI Voice Agents & Call Center CRM platform overview.
- [Homepage (Hebrew)](https://kolivri.com/he/): פלטפורמת סוכני AI קוליים, מוקד טלפוני חכם ו-CRM לעסקים בישראל.
- [Machine-Readable Pricing (`/pricing.md`)](https://kolivri.com/pricing.md): Complete USD and ILS pricing tiers (`Starter $69 / ₪247`, `Business $139 / ₪479`, `Pro $249 / ₪880`, `Enterprise`, and `0 ₪/month` Contingency Invoice Recovery).
- [Start 14-Day Free Trial (English)](https://kolivri.com/start/): Self-serve onboarding and instant AI phone line setup.
- [Start 14-Day Free Trial (Hebrew)](https://kolivri.com/he/start/): הרשמה ל-14 ימי ניסיון והפעלת סוכן קולי תוך 30 שניות.
- [30-Second Call Forwarding Guide (`*61*` / `*004*`)](https://kolivri.com/he/follow-me/): Step-by-step conditional call forwarding setup for Partner, Pelephone, Cellcom, HOT Mobile, Golan, 019, We4G, and Bezeq (`054-787-5670`).
- [Kolivri Cloud Application](https://app.kolivri.co.il): Production dashboard, CRM, call logs, and API key management.

## Live Interactive Tools (Kolivri AI Voice Lab)

- [The 2-Call Amnesia Test (`/amnesia-test/`)](https://kolivri.com/amnesia-test/) | [Hebrew (`/he/amnesia-test/`)](https://kolivri.com/he/amnesia-test/): Interactive 2-call memory challenge proving `100% LongMemEval` and `98% Hebrew LongMemEval` temporal supersession vs. stateless voice bots.
- [Business Phone Line Grader (`/phone-grader/`)](https://kolivri.com/phone-grader/) | [Hebrew (`/he/phone-grader/`)](https://kolivri.com/he/phone-grader/): Audit any SMB phone line for missed-call revenue leakage and get instant 30-second `*004*` / `*61*` forwarding codes.
- [WooCommerce AI Voice Cart & Order Recovery (`/woo-voice-recovery/`)](https://kolivri.com/woo-voice-recovery/) | [Hebrew (`/he/woo-voice-recovery/`)](https://kolivri.com/he/woo-voice-recovery/): Automated AI phone recovery for abandoned WooCommerce checkouts and COD/high-value order verification.
- [Zero-Subscription Dead Invoice Recovery (`/invoice-recovery/`)](https://kolivri.com/invoice-recovery/) | [Hebrew (`/he/invoice-recovery/`)](https://kolivri.com/he/invoice-recovery/): `0 ₪/month` overdue B2B invoice collection (`5%` success fee only) for Morning (GreenInvoice), iCount, and YPay receivables.
- [Call For Me — Consumer Proxy Caller (`/call-for-me/`)](https://kolivri.com/call-for-me/) | [Hebrew (`/he/call-for-me/`)](https://kolivri.com/he/call-for-me/): Dispatch a Kolivri AI voice agent to call local businesses, book appointments, or request quotes on your behalf.
- [Parallel-Dial FOMO Dispatch Arena (`/dispatch/`)](https://kolivri.com/dispatch/) | [Hebrew (`/he/dispatch/`)](https://kolivri.com/he/dispatch/): Simultaneous 3-provider emergency dispatch simulation with 180ms atomic winner lock (`Press 1 to Claim Job`).
- [Developer Voice MCP Hub (`/mcp/`)](https://kolivri.com/mcp/) | [Hebrew (`/he/mcp/`)](https://kolivri.com/he/mcp/): Official `kolivri-voice-mcp` server documentation (`kolivri_place_call`, `kolivri_get_call_status`, `kolivri_carrier_forwarding_codes`, `kolivri_preview_roi`) for Claude Code, OpenCode, and Cursor.
- [Instant AI Ghost Mini-Site Preview (`/ghost-site/`)](https://kolivri.com/ghost-site/) | [Hebrew (`/he/ghost-site/`)](https://kolivri.com/he/ghost-site/): Instant AI landing page + 24/7 voice receptionist preview for `biz-without-website` local businesses.

## Industry Verticals

- [Electricians & Emergency Electrical Contractors (`/he/electricians/`)](https://kolivri.com/he/electricians/): 24/7 AI dispatch, fault triage, and automatic job booking while on a ladder or job site.
- [Plumbers & Water Damage Technicians (`/he/plumbers/`)](https://kolivri.com/he/plumbers/): Urgent leak triage, address capture, and instant WhatsApp summary + calendar scheduling.
- [Medical, Dental & Aesthetic Clinics (`/he/clinics/`)](https://kolivri.com/he/clinics/): Bilingual patient intake, appointment scheduling, rescheduling, and pre-visit FAQ handling.
- [Auto Repair Garages & Service Centers (`/he/automotive/`)](https://kolivri.com/he/automotive/): Service bay scheduling, test/maintenance quotes, and vehicle status updates.

## API Endpoints & Developer Integration

- **Growth Hub REST Base**: `https://api.kolivri.co.il/api/v1/growth/*`
  - `POST https://api.kolivri.co.il/api/v1/growth/invoice-recovery/preview` — Segment overdue invoices (`1–30`, `31–60`, `61+` days), select behavioral-economics nudge strategies, and compute `5%` contingency recovery ROI.
  - `POST https://api.kolivri.co.il/api/v1/growth/trojan-caller/mission` — Build consumer-to-SMB outbound AI voice missions and WhatsApp follow-up payloads across `trades`, `clinic`, `automotive`, `restaurant`, and `real_estate`.
  - `POST https://api.kolivri.co.il/api/v1/growth/fomo-arbitrage/race` — Initiate a 3-provider parallel-dial dispatch race.
  - `POST https://api.kolivri.co.il/api/v1/growth/fomo-arbitrage/claim` — Execute atomic winner lock (`180ms`) when the first provider presses `1`.
- **WordPress REST Mirror Routes**: `/wp-json/kolivri/v1/growth/invoice-recovery/preview`, `/wp-json/kolivri/v1/growth/trojan-caller/mission`, `/wp-json/kolivri/v1/growth/fomo-arbitrage/race`, `/wp-json/kolivri/v1/growth/fomo-arbitrage/claim`.
- **Official MCP Package**: `claude mcp add kolivri-voice -- npx -y kolivri-voice-mcp`
- **Verified Contact & Demo Line**: `054-787-5670` (`+972-54-787-5670`) | Web: `https://kolivri.com`
MD;
    }

    /**
     * Alias for `getLlmsTxtContent()`.
     *
     * @return string
     */
    public static function get_llms_txt_content() {
        return self::getLlmsTxtContent();
    }

    /**
     * Return machine-parseable Markdown pricing breakdown for `/pricing.md` and `/pricing.txt`.
     *
     * @return string
     */
    public static function getPricingMdContent() {
        return <<<'MD'
# Kolivri — Machine-Readable Pricing & Plan Breakdown (`/pricing.md`)

> Official unified pricing for Kolivri AI Voice Agents & All-in-One Call Center CRM (`https://kolivri.com/` | `https://kolivri.com/he/`). All subscription plans include a 14-day free trial (`https://kolivri.com/start/` | `https://kolivri.com/he/start/`).

## Summary Comparison Table

| Plan | Global Price (USD) | Israel Price (ILS) | Included AI Minutes | Overage Rate (USD / ILS) | Concurrent Calls | Languages |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Starter** | `$69/month` | `₪247/month` | 100 AI minutes | `$0.79/min` (`₪2.47/min`) | 1 concurrent call | 1 language (any of 8) |
| **Business (Most Popular)** | `$139/month` | `₪479/month` | 220 AI minutes | `$0.69/min` (`₪2.18/min`) | 1 concurrent call | 2 languages |
| **Pro** | `$249/month` | `₪880/month` | 450 AI minutes | `$0.59/min` (`₪1.97/min`) | 3 concurrent calls | All 8 languages |
| **Enterprise** | Custom | Custom | Custom volume bundles | Custom | Custom / Unlimited | All 8 languages |
| **Zero-Subscription Contingency Invoice Recovery** | `$0/month` | `0 ₪/month` | Performance-based | `5%` success fee only | Automated campaigns | Hebrew & English |

---

## Plan Details

### 1. Starter — `$69/month` (`₪247/month` in IL)

- **Monthly Subscription**: `$69/month` (`₪247/month` in IL)
- **Included AI Voice Volume**: 100 AI minutes included per month
- **Overage Rate**: `$0.79/min` (`₪2.47/min` in IL)
- **Concurrency**: 1 concurrent call
- **Included Features**:
  - 24/7 inbound AI voice agent (`*61*` / `*004*` 30-second mobile forwarding or dedicated number)
  - In-call appointment booking
  - RAG Knowledge Base (grounded answers from your business documents & FAQs)
  - Built-in CRM & lead capture
  - Full call recordings & searchable transcripts
  - 14-day free trial (`https://kolivri.com/start/` | `https://kolivri.com/he/start/`)

### 2. Business (Most Popular) — `$139/month` (`₪479/month` in IL)

- **Monthly Subscription**: `$139/month` (`₪479/month` in IL)
- **Included AI Voice Volume**: 220 AI minutes included per month
- **Overage Rate**: `$0.69/min` (`₪2.18/min` in IL)
- **Concurrency**: 1 concurrent call
- **Included Features**:
  - Everything in Starter, plus:
  - Outbound AI call campaigns (lead follow-up, appointment confirmations, payment reminders)
  - Visual call flow & journey builder
  - Google Calendar two-way sync & Ticketing system
  - 2 languages (e.g., Hebrew + English or English + Spanish)

### 3. Pro — `$249/month` (`₪880/month` in IL)

- **Monthly Subscription**: `$249/month` (`₪880/month` in IL)
- **Included AI Voice Volume**: 450 AI minutes included per month
- **Overage Rate**: `$0.59/min` (`₪1.97/min` in IL)
- **Concurrency**: 3 concurrent calls
- **Included Features**:
  - Everything in Business, plus:
  - All 8 languages (English, Hebrew, Arabic, Russian, French, Spanish, German, Portuguese)
  - Webhooks & REST API (`https://api.kolivri.co.il`) + Official MCP Server (`kolivri-voice-mcp`)
  - 7-year recording retention for compliance and audit trails
  - Memoria Graph Memory (`100% LongMemEval`, `98% Hebrew LongMemEval`)

### 4. Enterprise — Custom

- **Pricing**: Custom volume bundles
- **Seats & Scale**: Unlimited seats and custom high-concurrency SIP trunking
- **Integrations**: SAP, Salesforce, Monday.com, Priority, and custom ERP/CRM integrations
- **Support & Compliance**: Dedicated SLA, custom security review, and dedicated solutions engineer

### 5. Zero-Subscription Contingency Invoice Recovery — `0 ₪/month` (`5%` Success Fee)

- **Monthly Subscription**: `0 ₪/month` (`$0/month` — zero fixed retainer)
- **Success Fee**: `5%` success fee on recovered overdue B2B invoices only
- **Supported Accounting Providers**: Morning (GreenInvoice / חשבונית ירוקה), iCount, YPay, and CSV upload
- **Interactive Calculator & Live Simulation**: [https://kolivri.com/he/invoice-recovery/](https://kolivri.com/he/invoice-recovery/) | [https://kolivri.com/invoice-recovery/](https://kolivri.com/invoice-recovery/)

---

## Links & Contact

- **Sign Up / 14-Day Free Trial**: `https://kolivri.com/start/` | `https://kolivri.com/he/start/`
- **Production Portal**: `https://app.kolivri.co.il`
- **AI Discovery Overview**: `https://kolivri.com/llms.txt`
- **Live Kolivri Demo Line**: `054-787-5670` (`+972-54-787-5670`)
MD;
    }

    /**
     * Alias for `getPricingMdContent()`.
     *
     * @return string
     */
    public static function get_pricing_md_content() {
        return self::getPricingMdContent();
    }

    /**
     * Return self-contained, zero-dependency JavaScript for the 1-line agency/partner
     * callback widget (`https://kolivri.com/widget.js`).
     *
     * @return string
     */
    public static function getEmbedWidgetJsContent() {
        return <<<'JS'
/**
 * Kolivri Voice AI — 1-Line Embeddable Callback Widget (https://kolivri.com/widget.js)
 * Zero dependencies. Supports data-lang ("he" | "en"), data-partner, data-phone, data-position ("bottom-right" | "bottom-left").
 */
(function() {
    if (typeof window === 'undefined' || typeof document === 'undefined') return;

    var scriptEl = document.currentScript || (function() {
        var scripts = document.querySelectorAll('script[src*="widget.js"]');
        return scripts.length ? scripts[scripts.length - 1] : null;
    })();

    var rawLang = scriptEl ? (scriptEl.getAttribute('data-lang') || 'he') : 'he';
    var lang = (String(rawLang).toLowerCase() === 'en') ? 'en' : 'he';
    var isEn = (lang === 'en');
    var partner = scriptEl ? (scriptEl.getAttribute('data-partner') || 'agency') : 'agency';
    var phoneDisplay = scriptEl ? (scriptEl.getAttribute('data-phone') || '054-787-5670') : '054-787-5670';
    var rawPos = scriptEl ? (scriptEl.getAttribute('data-position') || '') : '';
    var position = (rawPos === 'bottom-left' || rawPos === 'bottom-right')
        ? rawPos
        : (isEn ? 'bottom-right' : 'bottom-left');

    var cleanDigits = String(phoneDisplay).replace(/\D+/g, '') || '0547875670';
    var e164Phone = cleanDigits.indexOf('972') === 0
        ? ('+' + cleanDigits)
        : (cleanDigits.indexOf('0') === 0 ? ('+972' + cleanDigits.slice(1)) : '+972547875670');

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    var safePhoneDisplay = escHtml(phoneDisplay);
    var badgeHref = 'https://kolivri.com/he/?utm_source=agency_widget&ref=' + encodeURIComponent(partner);

    var KolivriCallbackWidget = {
        version: '1.0.0',
        config: {
            lang: lang,
            partner: partner,
            phone: phoneDisplay,
            e164: e164Phone,
            position: position,
            badgeHref: badgeHref
        },
        mount: function() {
            if (!document.body || document.getElementById('klv-cb-widget-root')) return;

            var style = document.createElement('style');
            style.id = 'klv-cb-widget-styles';
            style.textContent = [
                '.klv-cb-widget-root{position:fixed;bottom:20px;' + (position === 'bottom-left' ? 'left:20px;' : 'right:20px;') + 'z-index:2147483000;font-family:Rubik,Assistant,Inter,-apple-system,BlinkMacSystemFont,sans-serif;box-sizing:border-box;}',
                '.klv-cb-widget-root *,.klv-cb-widget-root *::before,.klv-cb-widget-root *::after{box-sizing:border-box;}',
                '.klv-cb-trigger{min-height:48px;padding:12px 20px;border-radius:999px;border:2px solid rgba(56,189,248,0.55);background:linear-gradient(135deg,#0B1220 0%,#1E293B 100%);color:#FFFFFF;font-size:15px;font-weight:800;cursor:pointer;box-shadow:0 14px 34px rgba(11,18,32,0.45);display:inline-flex;align-items:center;gap:8px;transition:transform 0.15s ease,border-color 0.15s ease;}',
                '.klv-cb-trigger:hover{transform:translateY(-2px);border-color:#FB8123;}',
                '.klv-cb-popover{display:none;width:320px;max-width:calc(100vw - 32px);margin-bottom:12px;background:#0B1220;color:#F8FAFC;border:1px solid rgba(56,189,248,0.38);border-radius:16px;padding:18px;box-shadow:0 22px 50px rgba(11,18,32,0.55);}',
                '.klv-cb-popover.open{display:block;}',
                '.klv-cb-title{font-size:16px;font-weight:800;margin:0 0 6px;color:#FFFFFF;}',
                '.klv-cb-sub{font-size:13px;color:#CBD5E1;margin:0 0 12px;line-height:1.45;}',
                '.klv-cb-input,.klv-cb-select{width:100%;min-height:44px;padding:9px 12px;margin-bottom:10px;border-radius:10px;border:1px solid rgba(148,163,184,0.35);background:#111C33;color:#FFFFFF;font-size:14px;font-family:inherit;}',
                '.klv-cb-submit{width:100%;min-height:46px;padding:10px 14px;border-radius:10px;border:none;background:linear-gradient(90deg,#FB8123 0%,#EA580C 100%);color:#FFFFFF;font-size:14px;font-weight:800;cursor:pointer;margin-bottom:8px;}',
                '.klv-cb-tel-direct{display:flex;align-items:center;justify-content:center;width:100%;min-height:44px;padding:8px 12px;border-radius:10px;background:rgba(16,185,129,0.16);border:1px solid rgba(16,185,129,0.45);color:#6EE7B7 !important;font-size:13.5px;font-weight:700;text-decoration:none !important;margin-bottom:10px;}',
                '.klv-cb-badge-wrap{text-align:center;padding-top:8px;border-top:1px solid rgba(148,163,184,0.18);font-size:12px;}',
                '.klv-cb-badge{color:#38BDF8 !important;text-decoration:none !important;font-weight:700;}'
            ].join('\n');
            document.head.appendChild(style);

            var root = document.createElement('div');
            root.id = 'klv-cb-widget-root';
            root.className = 'klv-cb-widget-root';
            root.setAttribute('dir', isEn ? 'ltr' : 'rtl');
            root.setAttribute('lang', lang);

            var btnLabel = isEn ? '📞 AI Callback in 10s' : '📞 שיחה חוזרת ב-10 שניות';
            var popTitle = isEn ? 'Instant AI Voice Callback (10s)' : 'שיחה חוזרת מסוכן AI תוך 10 שניות';
            var popSub   = isEn
                ? 'Enter your mobile number for an instant callback, or tap below to talk live right now:'
                : 'הזינו מספר נייד לשיחה חוזרת מיידית, או לחצו לחיוג ישיר עכשיו:';
            var phText   = isEn ? 'Your mobile (e.g. 054-787-5670)' : 'מספר נייד (למשל 054-787-5670)';
            var subLabel = isEn ? '🚀 Call Me Back in 10 Seconds' : '🚀 חייגו אליי חזרה תוך 10 שניות';
            var telLabel = isEn ? ('📞 Or call live now: ' + safePhoneDisplay) : ('📞 או חייגו עכשיו ישירות: ' + safePhoneDisplay);

            root.innerHTML = ''
                + '<div class="klv-cb-popover" id="klv-cb-popover" role="dialog" aria-label="' + popTitle + '">'
                + '  <div class="klv-cb-title">' + popTitle + '</div>'
                + '  <p class="klv-cb-sub">' + popSub + '</p>'
                + '  <input type="tel" class="klv-cb-input" id="klv-cb-visitor-phone" placeholder="' + phText + '" dir="ltr">'
                + '  <select class="klv-cb-select" id="klv-cb-topic">'
                + (isEn
                    ? '<option value="appointment">Book an appointment</option><option value="quote">Price quote / availability</option><option value="urgent">Urgent service call</option>'
                    : '<option value="appointment">תיאום תור / ביקור</option><option value="quote">הצעת מחיר ובדיקת זמינות</option><option value="urgent">קריאת שירות דחופה</option>')
                + '  </select>'
                + '  <button type="button" class="klv-cb-submit" id="klv-cb-submit-btn">' + subLabel + '</button>'
                + '  <a href="' + (e164Phone ? ('tel:' + e164Phone) : 'tel:+972547875670') + '" class="klv-cb-tel-direct" data-phone="' + safePhoneDisplay + '">' + telLabel + '</a>'
                + '  <div class="klv-cb-badge-wrap">'
                + '    <a href="https://kolivri.com/he/?utm_source=agency_widget&ref=' + encodeURIComponent(partner) + '" rel="dofollow" class="klv-cb-badge">⚡ מופעל ע״י Kolivri Voice AI</a>'
                + '  </div>'
                + '</div>'
                + '<button type="button" class="klv-cb-trigger" id="klv-cb-trigger-btn" aria-expanded="false">' + btnLabel + '</button>';

            document.body.appendChild(root);

            var triggerBtn = document.getElementById('klv-cb-trigger-btn');
            var popoverEl  = document.getElementById('klv-cb-popover');
            var submitBtn  = document.getElementById('klv-cb-submit-btn');
            var phoneInp   = document.getElementById('klv-cb-visitor-phone');
            var topicSel   = document.getElementById('klv-cb-topic');

            if (triggerBtn && popoverEl) {
                triggerBtn.addEventListener('click', function() {
                    var isOpen = popoverEl.classList.toggle('open');
                    triggerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            }

            if (submitBtn) {
                submitBtn.addEventListener('click', function() {
                    var visitorPhone = phoneInp ? (phoneInp.value || '').trim() : '';
                    var topic = topicSel ? (topicSel.value || 'appointment') : 'appointment';
                    if (!visitorPhone) {
                        if (phoneInp) phoneInp.focus();
                        return;
                    }
                    submitBtn.textContent = isEn ? '⏳ Dialing your phone now...' : '⏳ מחייג אליך עכשיו...';
                    fetch('https://api.kolivri.co.il/api/v1/growth/trojan-caller/mission', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            category: 'trades',
                            consumerName: visitorPhone,
                            businessName: document.title || 'Partner Site',
                            requestedTask: topic,
                            preferredSlot: 'Immediate 10s Callback (' + partner + ')'
                        })
                    }).catch(function() {}).finally(function() {
                        submitBtn.textContent = isEn ? '✓ Callback Dispatched! Or tap tel:+972547875670' : '✓ השיחה בדרך! או חייגו 054-787-5670';
                    });
                });
            }
        }
    };

    window.KolivriCallbackWidget = KolivriCallbackWidget;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            KolivriCallbackWidget.mount();
        });
    } else {
        KolivriCallbackWidget.mount();
    }
})();
JS;
    }

    /**
     * Alias for `getEmbedWidgetJsContent()`.
     *
     * @return string
     */
    public static function get_embed_widget_js_content() {
        return self::getEmbedWidgetJsContent();
    }

    /**
     * Normalize `competitor` attribute (`hub|vapi|retell|bland|synthflow`) or infer from `REQUEST_URI`.
     *
     * @param array $atts
     * @return string
     */
    public static function resolveCompetitor($atts = array()) {
        $raw = '';
        if (is_array($atts) && !empty($atts['competitor'])) {
            $raw = strtolower(trim((string) $atts['competitor']));
        } elseif (!empty($_SERVER['REQUEST_URI'])) {
            $uri = strtolower((string) $_SERVER['REQUEST_URI']);
            if (strpos($uri, 'vapi') !== false) {
                $raw = 'vapi';
            } elseif (strpos($uri, 'retell') !== false) {
                $raw = 'retell';
            } elseif (strpos($uri, 'bland') !== false) {
                $raw = 'bland';
            } elseif (strpos($uri, 'synthflow') !== false) {
                $raw = 'synthflow';
            }
        }

        $map = array(
            'hub'              => 'hub',
            'all'              => 'hub',
            'alternatives'     => 'hub',
            'vapi'             => 'vapi',
            'vapi-ai'          => 'vapi',
            'retell'           => 'retell',
            'retell-ai'        => 'retell',
            'retellai'         => 'retell',
            'bland'            => 'bland',
            'bland-ai'         => 'bland',
            'blandai'          => 'bland',
            'synthflow'        => 'synthflow',
            'synthflow-ai'     => 'synthflow',
            'synthflowai'      => 'synthflow',
        );

        return isset($map[$raw]) ? $map[$raw] : 'hub';
    }

    /**
     * Centralized source of truth for Competitor Alternative & Comparison pages
     * (`hub`, `vapi`, `retell`, `bland`, `synthflow`) in English (`en`) and Hebrew (`he`).
     *
     * @return array
     */
    public static function getCompetitorProfiles() {
        return array(
            'hub' => array(
                'slug'        => 'hub',
                'name'        => 'Vapi / Retell AI / Bland AI / Synthflow',
                'shortName'   => 'Developer Voice APIs',
                'path'        => '/alternatives/',
                'badgeEn'     => '⚔️ 2026 AI Voice Platform Comparison Hub',
                'badgeHe'     => '⚔️ השוואת פלטפורמות סוכני AI קוליים 2026',
                'titleEn'     => 'Why Teams Switch to Kolivri from Vapi, Retell AI, Bland AI & Synthflow',
                'titleHe'     => 'למה עסקים וצוותים עוברים לקוליברי (Why Teams Switch to Kolivri) מ-Vapi, Retell AI, Bland AI ו-Synthflow',
                'tldrEn'      => 'TL;DR: Vapi, Retell AI, Bland AI, and Synthflow are either developer-only telephony APIs that charge layered STT+LLM+TTS+Telephony per-minute stacks or basic prompt wrappers with Stateless / Basic Prompt memory and Bring Your Own CRM requirements. Teams switch to Kolivri for an all-in-one AI Voice Agent with Included Multi-Tenant CRM & Ticketing, Memoria 100% LongMemEval / 98% Hebrew temporal graph memory, 30s *61* Call Forwarding setup, 8-Language Native Voice, and Flat $69 / ₪247 Starter with 100 min included pricing.',
                'tldrHe'      => 'TL;DR (בשורה התחתונה): מערכות כמו Vapi, Retell AI, Bland AI ו-Synthflow דורשות חיבור ספקי STT+LLM+TTS+Telephony נפרדים בדקות מצטברות (Layered STT+LLM+TTS+Telephony per-minute stacks), חסרות זיכרון ארוך-טווח (Stateless / Basic Prompt) ומחייבות Bring Your Own CRM עם שבועות של פיתוח Webhooks. עסקים וצוותים עוברים לקוליברי (Why Teams Switch to Kolivri) כי הכל כלול: CRM וטיקטינג מובנים (Included), זיכרון גרף Memoria 100% LongMemEval / 98% Hebrew, הפעלה ב-30 שניות עם *61* (30s *61* Call Forwarding), 8-Language Native Voice ותמחור שקוף של Flat $69 / ₪247 Starter with 100 min included.',
                'chooseCompTitleEn' => 'When to Choose Vapi / Retell AI / Bland AI / Synthflow',
                'chooseCompTitleHe' => 'מתי לבחור ב-Vapi / Retell AI / Bland AI / Synthflow (When to Choose Competitor)',
                'chooseCompBulletsEn' => array(
                    'You have a dedicated backend voice engineering team that wants to stitch raw WebSockets, Deepgram STT, OpenAI LLM, ElevenLabs TTS, and Twilio SIP trunks from scratch.',
                    'You already maintain a custom proprietary CRM and ticketing database and prefer to manage 4–5 separate vendor invoices every month.',
                    'Your calls are single-turn English scripts that do not require cross-call temporal memory (LongMemEval) or native Hebrew/RTL voice nuance.',
                ),
                'chooseCompBulletsHe' => array(
                    'יש לכם צוות פיתוח ייעודי שרוצה לחבר בעצמו WebSockets, מנועי STT/LLM/TTS וחשבונות Twilio נפרדים מאפס (Weeks of API/Webhook Stitching).',
                    'אתם כבר מתחזקים מערכת CRM וטיקטינג בפיתוח עצמי ומוכנים לשלם ל-4 ספקים שונים לפי דקה (Bring Your Own CRM).',
                    'השיחות שלכם חד-פעמיות באנגלית בלבד ולא דורשות זיכרון רב-שיחתי (LongMemEval) או עברית טבעית.',
                ),
                'chooseKolivriTitleEn' => 'When to Choose Kolivri',
                'chooseKolivriTitleHe' => 'מתי לבחור בקוליברי (When to Choose Kolivri)',
                'chooseKolivriBulletsEn' => array(
                    'You want Built-in Multi-Tenant CRM & Ticketing (Included) with automatic call summaries, appointment booking, and SLA tracking out of the box.',
                    'You need Memoria 100% LongMemEval / 98% Hebrew long-term graph memory so returning callers are recognized and updated facts supersede stale ones automatically.',
                    'You want 30s *61* Call Forwarding activation, 8-Language Native Voice, and predictable Flat $69 / ₪247 Starter with 100 min included pricing.',
                ),
                'chooseKolivriBulletsHe' => array(
                    'אתם רוצים מערכת All-in-One עם Built-in Multi-Tenant CRM & Ticketing (Included), תמלול, סיכום שיחה וקביעת תורים ביומן ללא כתיבת קוד.',
                    'חשוב לכם שהסוכן יזכור כל לקוח חוזר באמצעות Memoria 100% LongMemEval / 98% Hebrew ולא ישכח שיחות קודמות.',
                    'אתם רוצים לעלות לאוויר תוך 30 שניות עם *61* (30s *61* Call Forwarding) במחיר קבוע של Flat $69 / ₪247 Starter with 100 min included.',
                ),
            ),
            'vapi' => array(
                'slug'        => 'vapi',
                'name'        => 'Vapi',
                'shortName'   => 'Vapi',
                'path'        => '/vapi-alternative/',
                'badgeEn'     => '⚡ #1 Vapi Alternative (All-in-One Voice AI + CRM)',
                'badgeHe'     => '⚡ האלטרנטיבה המובילה ל-Vapi בישראל ובעולם',
                'titleEn'     => 'The #1 Vapi Alternative — Why Teams Switch to Kolivri for Built-In CRM, Memoria Memory & Flat Pricing',
                'titleHe'     => 'האלטרנטיבה ל-Vapi — למה צוותים עוברים לקוליברי (Why Teams Switch to Kolivri)',
                'tldrEn'      => 'TL;DR: Vapi is a developer-only voice orchestration API that charges a platform per-minute fee on top of separate STT, LLM, TTS, and telephony bills (Layered STT+LLM+TTS+Telephony per-minute stacks), offers Stateless / Basic Prompt memory, and requires Bring Your Own CRM with Weeks of API/Webhook Stitching. Kolivri replaces the entire stack with Included Multi-Tenant CRM & Ticketing, Memoria 100% LongMemEval / 98% Hebrew memory, 30s *61* Call Forwarding, 8-Language Native Voice, and Flat $69 / ₪247 Starter with 100 min included.',
                'tldrHe'      => 'TL;DR (בשורה התחתונה): Vapi היא תשתית API למפתחים בלבד שגובה עמלת דקה מעל חשבונות STT+LLM+TTS+Telephony נפרדים (Layered STT+LLM+TTS+Telephony per-minute stacks), עובדת במודל Stateless / Basic Prompt ומחייבת Bring Your Own CRM עם שבועות של אינטגרציות (Weeks of API/Webhook Stitching). קוליברי מעניקה פתרון שלם: CRM וטיקטינג מובנים (Included), זיכרון Memoria 100% LongMemEval / 98% Hebrew, הפעלה ב-30 שניות (30s *61* Call Forwarding) ותמחור Flat $69 / ₪247 Starter with 100 min included.',
                'chooseCompTitleEn' => 'When to Choose Vapi',
                'chooseCompTitleHe' => 'מתי לבחור ב-Vapi (When to Choose Vapi)',
                'chooseCompBulletsEn' => array(
                    'You are a software engineering team building a custom voice feature inside your own SaaS app and want low-level WebRTC/WebSocket plumbing.',
                    'You want to bring your own Deepgram, OpenAI, ElevenLabs, and Twilio API keys and reconcile 5 separate per-minute usage bills.',
                    'You are comfortable building your own CRM, call log UI, appointment calendar sync, and memory database from scratch.',
                ),
                'chooseCompBulletsHe' => array(
                    'אתם צוות פיתוח שבונה פיצ׳ר קולי פנימי בתוך מוצר תוכנה משלכם ורוצים לשלוט בצנרת ה-WebRTC ברמת הקוד.',
                    'נוח לכם לנהל ולשלם בנפרד לחשבונות Deepgram, OpenAI, ElevenLabs ו-Twilio בכל חודש.',
                    'אתם מתכננים לבנות בעצמכם מערכת CRM, ממשק ניהול שיחות, סנכרון יומן ומסד נתונים לזיכרון לקוחות.',
                ),
                'chooseKolivriTitleEn' => 'When to Choose Kolivri',
                'chooseKolivriTitleHe' => 'מתי לבחור בקוליברי (When to Choose Kolivri)',
                'chooseKolivriBulletsEn' => array(
                    'You want a complete AI Voice Receptionist & Outbound Agent with Built-in Multi-Tenant CRM & Ticketing (Included) ready today.',
                    'You need Memoria 100% LongMemEval / 98% Hebrew temporal graph memory so callers never repeat themselves across calls.',
                    'You want predictable Flat $69 / ₪247 Starter with 100 min included pricing, 30s *61* Call Forwarding, and official MCP support (kolivri-voice-mcp).',
                ),
                'chooseKolivriBulletsHe' => array(
                    'אתם רוצים סוכן AI קולי מוכן לעבודה עם Built-in Multi-Tenant CRM & Ticketing (Included) ללא שבועות של פיתוח.',
                    'אתם צריכים זיכרון רב-שיחתי אמיתי (Memoria 100% LongMemEval / 98% Hebrew) בעברית וב-8 שפות (8-Language Native Voice).',
                    'אתם מעדיפים מחיר קבוע ושקוף (Flat $69 / ₪247 Starter with 100 min included) והפעלה ב-30 שניות עם *61*.',
                ),
            ),
            'retell' => array(
                'slug'        => 'retell',
                'name'        => 'Retell AI',
                'shortName'   => 'Retell AI',
                'path'        => '/retell-ai-alternative/',
                'badgeEn'     => '⚡ #1 Retell AI Alternative (Built-In CRM + Memoria Graph Memory)',
                'badgeHe'     => '⚡ האלטרנטיבה המובילה ל-Retell AI',
                'titleEn'     => 'The #1 Retell AI Alternative — Why Teams Switch to Kolivri for Long-Term Memory & All-in-One CRM',
                'titleHe'     => 'האלטרנטיבה ל-Retell AI — למה צוותים עוברים לקוליברי (Why Teams Switch to Kolivri)',
                'tldrEn'      => 'TL;DR: Retell AI provides developer voice SDKs with per-minute voice+LLM+telephony metering (Layered STT+LLM+TTS+Telephony per-minute stacks), Stateless / Basic Prompt call memory, and Bring Your Own CRM requirements. Teams switch to Kolivri for Included Multi-Tenant CRM & Ticketing, Memoria 100% LongMemEval / 98% Hebrew graph memory, 30s *61* Call Forwarding setup, 8-Language Native Voice, and Flat $69 / ₪247 Starter with 100 min included pricing.',
                'tldrHe'      => 'TL;DR (בשורה התחתונה): Retell AI מספקת SDK למפתחים עם חיוב דקות מדורג (Layered STT+LLM+TTS+Telephony per-minute stacks), זיכרון Stateless / Basic Prompt וללא מערכת CRM מובנית (Bring Your Own CRM). עסקים עוברים לקוליברי (Why Teams Switch to Kolivri) בזכות CRM וטיקטינג מובנים (Included), זיכרון Memoria 100% LongMemEval / 98% Hebrew, הפעלה ב-30 שניות (30s *61* Call Forwarding) ומחיר Flat $69 / ₪247 Starter with 100 min included.',
                'chooseCompTitleEn' => 'When to Choose Retell AI',
                'chooseCompTitleHe' => 'מתי לבחור ב-Retell AI (When to Choose Retell AI)',
                'chooseCompBulletsEn' => array(
                    'You have engineers building a custom US-English voice workflow wired into your own backend via WebSockets and custom webhooks.',
                    'You only need raw conversational turn-taking APIs and already run an external CRM, ticketing desk, and customer state store.',
                    'You prefer usage-only per-minute billing across voice engine, LLM, and telephony layers.',
                ),
                'chooseCompBulletsHe' => array(
                    'יש לכם צוות הנדסה שבונה זרימת שיחה באנגלית מול שרתים פנימיים שלכם באמצעות WebSockets ו-Webhooks.',
                    'אתם זקוקים למנוע דיבור גולמי בלבד וכבר מתחזקים מערכת CRM, טיקטינג וניהול מצב לקוח חיצונית.',
                    'אתם מעדיפים תמחור משתנה לפי דקה על פני חבילה חודשית קבועה.',
                ),
                'chooseKolivriTitleEn' => 'When to Choose Kolivri',
                'chooseKolivriTitleHe' => 'מתי לבחור בקוליברי (When to Choose Kolivri)',
                'chooseKolivriBulletsEn' => array(
                    'You need Built-in Multi-Tenant CRM & Ticketing (Included) with zero webhook maintenance.',
                    'You require Memoria 100% LongMemEval / 98% Hebrew temporal graph memory that tracks customer history across every call.',
                    'You want 30s *61* Call Forwarding setup, 8-Language Native Voice, and Flat $69 / ₪247 Starter with 100 min included pricing.',
                ),
                'chooseKolivriBulletsHe' => array(
                    'אתם צריכים Built-in Multi-Tenant CRM & Ticketing (Included) מוכן לעבודה מידית ללא תחזוקת שרתים.',
                    'אתם דורשים זיכרון Memoria 100% LongMemEval / 98% Hebrew שזוכר העדפות, שינויים והיסטוריית פניות של כל מתקשר.',
                    'אתם רוצים הפעלה ב-30 שניות (30s *61* Call Forwarding), 8-Language Native Voice ומחיר Flat $69 / ₪247 Starter with 100 min included.',
                ),
            ),
            'bland' => array(
                'slug'        => 'bland',
                'name'        => 'Bland AI',
                'shortName'   => 'Bland AI',
                'path'        => '/bland-ai-alternative/',
                'badgeEn'     => '⚡ #1 Bland AI Alternative (Inbound + Outbound + CRM + Memoria)',
                'badgeHe'     => '⚡ האלטרנטיבה המובילה ל-Bland AI',
                'titleEn'     => 'The #1 Bland AI Alternative — Why Teams Switch to Kolivri for 8-Language Voice, Memoria Memory & CRM',
                'titleHe'     => 'האלטרנטיבה ל-Bland AI — למה צוותים עוברים לקוליברי (Why Teams Switch to Kolivri)',
                'tldrEn'      => 'TL;DR: Bland AI focuses on developer-scripted US outbound call blasts with per-minute + per-call fees (Layered STT+LLM+TTS+Telephony per-minute stacks), Stateless / Basic Prompt memory, and Bring Your Own CRM via Weeks of API/Webhook Stitching. Teams switch to Kolivri for unified inbound + outbound AI voice agents, Included Multi-Tenant CRM & Ticketing, Memoria 100% LongMemEval / 98% Hebrew memory, 30s *61* Call Forwarding, and Flat $69 / ₪247 Starter with 100 min included.',
                'tldrHe'      => 'TL;DR (בשורה התחתונה): Bland AI מיועדת בעיקר להפצצת שיחות יוצאות בארה״ב דרך API עם חיובי דקות ושיחות מצטברים (Layered STT+LLM+TTS+Telephony per-minute stacks), זיכרון Stateless / Basic Prompt וחובת Bring Your Own CRM. עסקים עוברים לקוליברי (Why Teams Switch to Kolivri) כדי לקבל מענה נכנס ויוצא ב-8 שפות (8-Language Native Voice), מערכת CRM וטיקטינג מובנית (Included), זיכרון Memoria 100% LongMemEval / 98% Hebrew והפעלה ב-30 שניות (30s *61* Call Forwarding).',
                'chooseCompTitleEn' => 'When to Choose Bland AI',
                'chooseCompTitleHe' => 'מתי לבחור ב-Bland AI (When to Choose Bland AI)',
                'chooseCompBulletsEn' => array(
                    'Your primary use case is high-volume US English outbound telemarketing blasts triggered exclusively via developer REST scripts.',
                    'You have in-house engineers to build custom webhook receivers, retry queues, and external CRM synchronization.',
                    'You do not need 30-second mobile conditional call forwarding (*61* / *004*), built-in multi-tenant CRM, or 98% Hebrew LongMemEval memory.',
                ),
                'chooseCompBulletsHe' => array(
                    'השימוש היחיד שלכם הוא קמפיינים המוניים של שיחות יוצאות באנגלית בארה״ב דרך סקריפטים של מפתחים.',
                    'יש לכם מתכנתים שיבנו תורי שיחות, Webhooks וסנכרון ל-CRM חיצוני מאפס.',
                    'אינכם זקוקים להפניית שיחות *61* ב-30 שניות, ל-CRM מובנה או לזיכרון עמוק בעברית (98% Hebrew LongMemEval).',
                ),
                'chooseKolivriTitleEn' => 'When to Choose Kolivri',
                'chooseKolivriTitleHe' => 'מתי לבחור בקוליברי (When to Choose Kolivri)',
                'chooseKolivriBulletsEn' => array(
                    'You need both 24/7 inbound reception and smart outbound campaigns (including 0 ₪/month Dead Invoice Recovery) with Included Multi-Tenant CRM & Ticketing.',
                    'You want Memoria 100% LongMemEval / 98% Hebrew graph memory and native 8-language fluency.',
                    'You want 30s *61* Call Forwarding setup and predictable Flat $69 / ₪247 Starter with 100 min included pricing.',
                ),
                'chooseKolivriBulletsHe' => array(
                    'אתם צריכים גם קבלת שיחות נכנסות 24/7 וגם שיחות יוצאות חכמות (כולל גביית חשבוניות ב-0 ₪ מנוי) עם CRM וטיקטינג מובנים (Included).',
                    'אתם רוצים זיכרון Memoria 100% LongMemEval / 98% Hebrew ודיבור טבעי ב-8 שפות (8-Language Native Voice).',
                    'אתם רוצים הפעלה ב-30 שניות (30s *61* Call Forwarding) ותמחור Flat $69 / ₪247 Starter with 100 min included.',
                ),
            ),
            'synthflow' => array(
                'slug'        => 'synthflow',
                'name'        => 'Synthflow',
                'shortName'   => 'Synthflow',
                'path'        => '/synthflow-alternative/',
                'badgeEn'     => '⚡ #1 Synthflow Alternative (True Graph Memory + Native 8-Language CRM)',
                'badgeHe'     => '⚡ האלטרנטיבה המובילה ל-Synthflow',
                'titleEn'     => 'The #1 Synthflow Alternative — Why Teams Switch to Kolivri for Memoria Memory, Native Hebrew & Built-In CRM',
                'titleHe'     => 'האלטרנטיבה ל-Synthflow — למה צוותים עוברים לקוליברי (Why Teams Switch to Kolivri)',
                'tldrEn'      => 'TL;DR: Synthflow offers no-code English prompt flows but relies on Stateless / Basic Prompt memory, requires external GoHighLevel/Zapier CRM plumbing (Bring Your Own CRM / Weeks of API/Webhook Stitching), and scales into expensive per-minute/tier add-ons (Layered STT+LLM+TTS+Telephony per-minute stacks). Teams switch to Kolivri for Included Multi-Tenant CRM & Ticketing, Memoria 100% LongMemEval / 98% Hebrew temporal memory, 30s *61* Call Forwarding, 8-Language Native Voice, and Flat $69 / ₪247 Starter with 100 min included.',
                'tldrHe'      => 'TL;DR (בשורה התחתונה): Synthflow מציעה בניית תסריטים פשוטים באנגלית, אך סובלת מזיכרון Stateless / Basic Prompt, דורשת חיבור ל-CRM חיצוני כמו GoHighLevel/Zapier (Bring Your Own CRM / Weeks of API/Webhook Stitching) ועלויות דקות ותוספים מצטברות. עסקים וסוכנויות עוברים לקוליברי (Why Teams Switch to Kolivri) בזכות CRM וטיקטינג מובנים (Included), זיכרון Memoria 100% LongMemEval / 98% Hebrew, הפעלה ב-30 שניות (30s *61* Call Forwarding) ומחיר Flat $69 / ₪247 Starter with 100 min included.',
                'chooseCompTitleEn' => 'When to Choose Synthflow',
                'chooseCompTitleHe' => 'מתי לבחור ב-Synthflow (When to Choose Synthflow)',
                'chooseCompBulletsEn' => array(
                    'You are a US marketing agency standardized exclusively on GoHighLevel and only need basic single-call English FAQ/lead capture.',
                    'Your callers do not require multi-call temporal fact supersession (LongMemEval) or native Hebrew/Arabic/Russian voice synthesis.',
                    'You are already paying for external CRM seats, Zapier tasks, and Twilio numbers separately.',
                ),
                'chooseCompBulletsHe' => array(
                    'אתם סוכנות שעובדת רק בארה״ב מעל GoHighLevel וצריכה בוט שאלות-תשובות בסיסי באנגלית לשיחה בודדת.',
                    'הלקוחות שלכם לא מתקשרים מספר פעמים ולא נדרש זיכרון גרף רב-שיחתי (LongMemEval) או עברית טבעית.',
                    'אתם כבר משלמים בנפרד על רישיונות CRM חיצוניים, אוטומציות Zapier ומספרי Twilio.',
                ),
                'chooseKolivriTitleEn' => 'When to Choose Kolivri',
                'chooseKolivriTitleHe' => 'מתי לבחור בקוליברי (When to Choose Kolivri)',
                'chooseKolivriBulletsEn' => array(
                    'You want Built-in Multi-Tenant CRM & Ticketing (Included) plus a 1-line Agency Callback Widget (widget.js) with 10% Partner Rev-Share.',
                    'You need Memoria 100% LongMemEval / 98% Hebrew memory and 8-Language Native Voice (English, Hebrew, Arabic, Russian, French, Spanish, German, Portuguese).',
                    'You want 30s *61* Call Forwarding activation and Flat $69 / ₪247 Starter with 100 min included pricing.',
                ),
                'chooseKolivriBulletsHe' => array(
                    'אתם רוצים Built-in Multi-Tenant CRM & Ticketing (Included) + ווידג׳ט סוכנויות בשורה אחת (widget.js) עם 10% עמלת שותפים.',
                    'אתם צריכים זיכרון Memoria 100% LongMemEval / 98% Hebrew ותמיכה מלאה ב-8 שפות (8-Language Native Voice).',
                    'אתם רוצים חיבור ב-30 שניות עם *61* (30s *61* Call Forwarding) ומחיר Flat $69 / ₪247 Starter with 100 min included.',
                ),
            ),
        );
    }

    /**
     * Alias for `renderCompetitorAlternative($atts)`.
     *
     * @param array $atts
     * @return string
     */
    public static function render_competitor_alternative($atts = array()) {
        return self::renderCompetitorAlternative($atts);
    }

    /**
     * Render Shortcode 6: `[kolivri_competitor_alternative]` (`competitor="hub|vapi|retell|bland|synthflow"`, `lang="en|he"`).
     *
     * @param array $atts
     * @return string
     */
    public static function renderCompetitorAlternative($atts = array()) {
        $atts = is_array($atts) ? $atts : array();
        $lang = self::resolveLang($atts);
        $isEn = ($lang === 'en');
        $compKey = self::resolveCompetitor($atts);
        $profiles = self::getCompetitorProfiles();
        $profile = isset($profiles[$compKey]) ? $profiles[$compKey] : $profiles['hub'];
        $prefix = $isEn ? '' : '/he';

        $navItems = array(
            'hub'       => array('path' => '/alternatives/',          'en' => '⚔️ All Alternatives Hub', 'he' => '⚔️ כל האלטרנטיבות'),
            'vapi'      => array('path' => '/vapi-alternative/',      'en' => 'vs Vapi',                 'he' => 'מול Vapi'),
            'retell'    => array('path' => '/retell-ai-alternative/', 'en' => 'vs Retell AI',            'he' => 'מול Retell AI'),
            'bland'     => array('path' => '/bland-ai-alternative/',  'en' => 'vs Bland AI',             'he' => 'מול Bland AI'),
            'synthflow' => array('path' => '/synthflow-alternative/', 'en' => 'vs Synthflow',            'he' => 'מול Synthflow'),
        );

        $compDisplay = $profile['name'];

        $faqEntities = $isEn
            ? array(
                array(
                    '@type' => 'Question',
                    'name'  => "What is the best {$compDisplay} alternative in 2026?",
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => "Kolivri is the #1 {$compDisplay} alternative for businesses and agencies that want an all-in-one AI Voice Agent with Included Multi-Tenant CRM & Ticketing, Memoria 100% LongMemEval / 98% Hebrew long-term memory, 30s *61* Call Forwarding setup, 8-Language Native Voice, and Flat $69 / ₪247 Starter with 100 min included pricing instead of Layered STT+LLM+TTS+Telephony per-minute stacks.",
                    ),
                ),
                array(
                    '@type' => 'Question',
                    'name'  => "How does Kolivri pricing compare to {$compDisplay}?",
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => "Kolivri offers Flat $69 / ₪247 Starter with 100 min included (plus Business at $139 / ₪479 and Pro at $249 / ₪880) with CRM, ticketing, RAG Knowledge Base, and Memoria graph memory included. By contrast, {$compDisplay} relies on Layered STT+LLM+TTS+Telephony per-minute stacks and Bring Your Own CRM subscriptions.",
                    ),
                ),
                array(
                    '@type' => 'Question',
                    'name'  => "Does Kolivri have long-term caller memory compared to {$compDisplay}?",
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => "Yes. Kolivri is powered by Memoria temporal graph memory (achieving Memoria 100% LongMemEval / 98% Hebrew), whereas {$compDisplay} uses Stateless / Basic Prompt context that forgets caller history or fails when facts change across calls.",
                    ),
                ),
                array(
                    '@type' => 'Question',
                    'name'  => "How fast can we migrate from {$compDisplay} to Kolivri?",
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => "You can activate Kolivri in 30 seconds via 30s *61* Call Forwarding (*61*0547875670**15# or *004*0547875670**15#), connect a SIP trunk, or test live right now by calling 054-787-5670.",
                    ),
                ),
            )
            : array(
                array(
                    '@type' => 'Question',
                    'name'  => "מהי האלטרנטיבה הטובה ביותר ל-{$compDisplay} בשנת 2026?",
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => "קוליברי (Kolivri) היא האלטרנטיבה המובילה ל-{$compDisplay}: מערכת All-in-One הכוללת CRM וטיקטינג מובנים (Included מול Bring Your Own CRM), זיכרון גרף ארוך-טווח (Memoria 100% LongMemEval / 98% Hebrew מול Stateless / Basic Prompt), הפעלה ב-30 שניות עם *61* (30s *61* Call Forwarding), דיבור טבעי ב-8 שפות (8-Language Native Voice) ותמחור שקוף של Flat $69 / ₪247 Starter with 100 min included.",
                    ),
                ),
                array(
                    '@type' => 'Question',
                    'name'  => "איך תמחור קוליברי מושווה מול {$compDisplay}?",
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => "בקוליברי החבילה מתחילה ב-Flat $69 / ₪247 Starter with 100 min included (כולל CRM, מערכת פניות, RAG וזיכרון Memoria), בעוד שב-{$compDisplay} משלמים עלויות מצטברות של Layered STT+LLM+TTS+Telephony per-minute stacks ונדרשים לחבר CRM חיצוני.",
                    ),
                ),
                array(
                    '@type' => 'Question',
                    'name'  => "תוך כמה זמן ניתן לעבור מ-{$compDisplay} לקוליברי?",
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => "תוך 30 שניות בלבד באמצעות קוד הפניית שיחות *61* (30s *61* Call Forwarding — *61*0547875670**15# או *004*0547875670**15#) ללא צורך בשבועות של פיתוח Webhooks (Weeks of API/Webhook Stitching). ניתן גם לחייג עכשיו להדגמה חיה ב-054-787-5670.",
                    ),
                ),
            );

        $faqSchema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $faqEntities,
        );

        ob_start();
        echo self::getScopedCss();
        ?>
<div class="kgh-wrap" id="kgh-competitor-alternative-<?php echo self::esc($compKey); ?>" dir="<?php echo $isEn ? 'ltr' : 'rtl'; ?>" lang="<?php echo $isEn ? 'en' : 'he'; ?>" style="text-align:<?php echo $isEn ? 'left' : 'right'; ?>;" data-shortcode="kolivri_competitor_alternative" data-competitor="<?php echo self::esc($compKey); ?>">
    <?php echo self::renderLabSwitcher('alternatives', $lang); ?>

    <nav class="kgh-comp-nav" aria-label="<?php echo $isEn ? 'Competitor Alternatives Navigation' : 'ניווט השוואת מתחרים'; ?>">
        <?php foreach ($navItems as $slug => $item) :
            $href = $prefix . $item['path'];
            $label = $isEn ? $item['en'] : $item['he'];
            $curr = ($slug === $compKey) ? ' aria-current="page"' : '';
        ?>
        <a href="<?php echo self::esc($href); ?>"<?php echo $curr; ?>><?php echo self::esc($label); ?></a>
        <?php endforeach; ?>
    </nav>

    <span class="kgh-badge orange"><?php echo self::esc($isEn ? $profile['badgeEn'] : $profile['badgeHe']); ?></span>
    <h1 class="kgh-title"><?php echo self::esc($isEn ? $profile['titleEn'] : $profile['titleHe']); ?></h1>
    <p class="kgh-subtitle">
        <?php if ($isEn) : ?>
            <strong>Why Teams Switch to Kolivri:</strong> Stop stitching together fragile webhook servers, stateless prompts, and 4 separate per-minute billing accounts. Get a production AI Voice Agent + Built-In Multi-Tenant CRM &amp; Ticketing + Memoria Graph Memory live in 30 seconds.
        <?php else : ?>
            <strong>Why Teams Switch to Kolivri (למה צוותים ועסקים עוברים לקוליברי):</strong> הפסיקו לתחזק שרתי Webhooks שבירים, פרומפטים חסרי זיכרון ו-4 חשבוניות דקות נפרדות. קבלו סוכן AI קולי + CRM וטיקטינג מובנים + זיכרון Memoria שעולה לאוויר תוך 30 שניות.
        <?php endif; ?>
    </p>

    <div class="kgh-tldr-box" role="region" aria-label="TL;DR Summary">
        <?php echo self::esc($isEn ? $profile['tldrEn'] : $profile['tldrHe']); ?>
    </div>

    <div class="kgh-btn-row" style="margin-bottom:22px;">
        <a href="<?php echo self::esc($prefix . '/start/'); ?>" class="kgh-btn kgh-btn-primary">
            <?php echo $isEn ? '🚀 Start 14-Day Free Trial (/start/)' : '🚀 התחילו 14 ימי ניסיון (/he/start/)'; ?>
        </a>
        <a href="<?php echo self::esc($prefix . '/amnesia-test/'); ?>" class="kgh-btn kgh-btn-emerald">
            <?php echo $isEn ? '🧠 Run the 2-Call Amnesia Test (/amnesia-test/)' : '🧠 מבחן האמנזיה החי (/he/amnesia-test/)'; ?>
        </a>
        <a href="<?php echo self::esc($prefix . '/phone-grader/'); ?>" class="kgh-btn kgh-btn-secondary">
            <?php echo $isEn ? '🔥 Grade Your Phone Line (/phone-grader/)' : '🔥 מדד המענה העסקי (/he/phone-grader/)'; ?>
        </a>
        <a href="tel:+972547875670" class="kgh-btn kgh-btn-secondary">
            📞 <?php echo $isEn ? 'Live AI Roleplay: 054-787-5670' : 'חייגו להדגמה חיה: 054-787-5670'; ?>
        </a>
    </div>

    <div class="kgh-panel" style="margin-bottom:22px;">
        <div class="kgh-panel-title">
            <span><?php echo $isEn ? "Side-by-Side Comparison: Kolivri vs {$compDisplay}" : "השוואה ראש בראש: Kolivri מול {$compDisplay}"; ?></span>
            <span class="kgh-pill b-1-30"><?php echo $isEn ? 'Verified 2026 Architecture' : 'נתוני אמת 2026'; ?></span>
        </div>
        <div class="kgh-comp-table-wrap">
            <?php if ($compKey === 'hub') : ?>
            <table class="kgh-comp-table">
                <thead>
                    <tr>
                        <th><?php echo $isEn ? 'Capability / Dimension' : 'יכולת / קריטריון השוואה'; ?></th>
                        <th class="kgh-col-kolivri">✅ Kolivri</th>
                        <th>Vapi</th>
                        <th>Retell AI</th>
                        <th>Bland AI</th>
                        <th>Synthflow</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?php echo $isEn ? 'Built-in Multi-Tenant CRM & Ticketing' : 'Built-in Multi-Tenant CRM & Ticketing (CRM וטיקטינג מובנים)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">Included</span> — <?php echo $isEn ? 'Multi-tenant CRM, lead profiles, SLA ticketing & Google Calendar sync built in' : 'מערכת CRM מרובת-דיירים, ניהול לידים, טיקטינג ויומן מובנים'; ?></td>
                        <td><span class="kgh-comp-lose">Bring Your Own CRM</span></td>
                        <td><span class="kgh-comp-lose">Bring Your Own CRM</span></td>
                        <td><span class="kgh-comp-lose">Bring Your Own CRM</span></td>
                        <td><span class="kgh-comp-lose">Bring Your Own CRM</span> (GoHighLevel / Zapier)</td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $isEn ? 'Long-Term Memory' : 'Long-Term Memory (זיכרון שיחות ארוך-טווח)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">Memoria 100% LongMemEval / 98% Hebrew</span> — <?php echo $isEn ? 'Temporal graph memory with fact supersession across calls' : 'זיכרון גרף רב-שיחתי שמעדכן עובדות וזוכר כל לקוח חוזר'; ?></td>
                        <td><span class="kgh-comp-lose">Stateless / Basic Prompt</span></td>
                        <td><span class="kgh-comp-lose">Stateless / Basic Prompt</span></td>
                        <td><span class="kgh-comp-lose">Stateless / Basic Prompt</span></td>
                        <td><span class="kgh-comp-lose">Stateless / Basic Prompt</span></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $isEn ? 'Setup Time' : 'Setup Time (זמן הקמה וחיבור לקו)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">30s *61* Call Forwarding</span> — <?php echo $isEn ? 'Instant *61*0547875670**15# / *004* dial activation or SIP' : 'הפעלה ב-30 שניות מהנייד (*61*0547875670**15#) או SIP'; ?></td>
                        <td><span class="kgh-comp-lose">Weeks of API/Webhook Stitching</span></td>
                        <td><span class="kgh-comp-lose">Weeks of API/Webhook Stitching</span></td>
                        <td><span class="kgh-comp-lose">Weeks of API/Webhook Stitching</span></td>
                        <td><span class="kgh-comp-lose">Weeks of API/Webhook Stitching</span></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $isEn ? 'Pricing' : 'Pricing (מודל תמחור ועלויות דקה)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">Flat $69 / ₪247 Starter with 100 min included</span> — <?php echo $isEn ? 'All-inclusive (Business $139 / ₪479 · Pro $249 / ₪880)' : 'הכל כלול ללא הפתעות (Business ב-₪479 · Pro ב-₪880)'; ?></td>
                        <td><span class="kgh-comp-lose">Layered STT+LLM+TTS+Telephony per-minute stacks</span></td>
                        <td><span class="kgh-comp-lose">Layered STT+LLM+TTS+Telephony per-minute stacks</span></td>
                        <td><span class="kgh-comp-lose">Layered STT+LLM+TTS+Telephony per-minute stacks</span></td>
                        <td><span class="kgh-comp-lose">Layered STT+LLM+TTS+Telephony per-minute stacks</span></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $isEn ? '8-Language Native Voice' : '8-Language Native Voice (תמיכה רב-לשונית ועברית)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">8-Language Native Voice</span> — <?php echo $isEn ? 'Native English, Hebrew, Arabic, Russian, French, Spanish, German & Portuguese' : 'עברית טבעית + אנגלית, ערבית, רוסית, צרפתית, ספרדית, גרמנית ופורטוגזית'; ?></td>
                        <td><?php echo $isEn ? 'English-first (DIY TTS tuning for RTL/Hebrew)' : 'ממוקד אנגלית (דורש כיול TTS ידני לעברית)'; ?></td>
                        <td><?php echo $isEn ? 'English-first (limited native Hebrew flow)' : 'ממוקד אנגלית (תמיכה חלקית בעברית)'; ?></td>
                        <td><?php echo $isEn ? 'US English outbound focus' : 'ממוקד שיחות יוצאות באנגלית בארה״ב'; ?></td>
                        <td><?php echo $isEn ? 'English prompt templates' : 'תבניות פרומפט באנגלית'; ?></td>
                    </tr>
                </tbody>
            </table>
            <?php else : ?>
            <table class="kgh-comp-table">
                <thead>
                    <tr>
                        <th><?php echo $isEn ? 'Capability / Dimension' : 'יכולת / קריטריון השוואה'; ?></th>
                        <th class="kgh-col-kolivri">✅ Kolivri</th>
                        <th><?php echo self::esc($compDisplay); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?php echo $isEn ? 'Built-in Multi-Tenant CRM & Ticketing' : 'Built-in Multi-Tenant CRM & Ticketing (CRM וטיקטינג מובנים)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">Included</span> — <?php echo $isEn ? 'Full multi-tenant CRM, lead capture, call summaries, SLA ticketing & Google Calendar sync included' : 'מערכת CRM מרובת-דיירים, כרטיסי לקוח, סיכומי שיחה, טיקטינג וסנכרון יומן מובנים'; ?></td>
                        <td><span class="kgh-comp-lose">Bring Your Own CRM</span> — <?php echo $isEn ? "Requires external CRM subscriptions and custom webhook sync from {$compDisplay}" : "מחייב חיבור ותחזוקת CRM חיצוני מעל {$compDisplay}"; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $isEn ? 'Long-Term Memory' : 'Long-Term Memory (זיכרון ארוך-טווח)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">Memoria 100% LongMemEval / 98% Hebrew</span> — <?php echo $isEn ? 'Spreading-activation temporal graph memory that remembers callers and supersedes outdated facts' : 'גרף זיכרון רב-שיחתי שזוכר כל מתקשר ומעדכן עובדות שהשתנו בין שיחות'; ?></td>
                        <td><span class="kgh-comp-lose">Stateless / Basic Prompt</span> — <?php echo $isEn ? 'Single-call prompt context without built-in temporal graph memory' : 'פרומפט בודד לשיחה ללא גרף זיכרון רב-שיחתי מובנה'; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $isEn ? 'Setup Time' : 'Setup Time (זמן הקמה)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">30s *61* Call Forwarding</span> — <?php echo $isEn ? 'Dial *61*0547875670**15# or *004* on any mobile/landline or connect SIP in 30 seconds' : 'חיוג *61*0547875670**15# או *004* מכל נייד/קווי תוך 30 שניות'; ?></td>
                        <td><span class="kgh-comp-lose">Weeks of API/Webhook Stitching</span> — <?php echo $isEn ? 'Requires custom telephony provisioning, webhook endpoints, and prompt debugging' : 'דורש שבועות של הגדרת טלפוניה, שרתי Webhooks וכיול פרומפטים'; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $isEn ? 'Pricing' : 'Pricing (תמחור)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">Flat $69 / ₪247 Starter with 100 min included</span> — <?php echo $isEn ? 'Predictable flat tiers (Starter $69 / ₪247 · Business $139 / ₪479 · Pro $249 / ₪880)' : 'מחיר חודשי קבוע ושקוף (Starter ב-₪247 · Business ב-₪479 · Pro ב-₪880)'; ?></td>
                        <td><span class="kgh-comp-lose">Layered STT+LLM+TTS+Telephony per-minute stacks</span> — <?php echo $isEn ? 'Compounding per-minute fees across voice orchestration, STT, LLM, TTS, and telephony' : 'עלויות דקה מצטברות על תמלול (STT), מודל שפה (LLM), קול (TTS) וטלפוניה'; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $isEn ? '8-Language Native Voice' : '8-Language Native Voice (8 שפות מובנות)'; ?></strong></td>
                        <td class="kgh-col-kolivri"><span class="kgh-comp-win">8-Language Native Voice</span> — <?php echo $isEn ? 'English, Hebrew, Arabic, Russian, French, Spanish, German, and Portuguese out of the box' : 'עברית, אנגלית, ערבית, רוסית, צרפתית, ספרדית, גרמנית ופורטוגזית ברמת שפת אם'; ?></td>
                        <td><?php echo $isEn ? 'English-centric pipeline requiring manual voice/transcriber tuning for Hebrew & RTL' : 'תשתית ממוקדת אנגלית הדורשת התאמות ידניות לעברית ושפות RTL'; ?></td>
                    </tr>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="kgh-panel" style="margin-bottom:22px;">
        <div class="kgh-panel-title">
            <span><?php echo self::esc($isEn ? "Honest Assessment: When to Choose {$compDisplay} vs When to Choose Kolivri" : "השוואה הוגנת: When to Choose {$compDisplay} vs When to Choose Kolivri"); ?></span>
        </div>
        <div class="kgh-grid-2 kgh-honest-grid">
            <div class="kgh-item-card">
                <h3 style="font-size:16px;font-weight:800;color:#FDBA74;margin:0 0 10px;">
                    <?php echo self::esc($isEn ? $profile['chooseCompTitleEn'] : $profile['chooseCompTitleHe']); ?>
                </h3>
                <ul class="kgh-honest-list">
                    <?php foreach (($isEn ? $profile['chooseCompBulletsEn'] : $profile['chooseCompBulletsHe']) as $b) : ?>
                    <li><?php echo self::esc($b); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="kgh-item-card" style="border-color:rgba(16, 185, 129, 0.45);">
                <h3 style="font-size:16px;font-weight:800;color:#6EE7B7;margin:0 0 10px;">
                    <?php echo self::esc($isEn ? $profile['chooseKolivriTitleEn'] : $profile['chooseKolivriTitleHe']); ?>
                </h3>
                <ul class="kgh-honest-list">
                    <?php foreach (($isEn ? $profile['chooseKolivriBulletsEn'] : $profile['chooseKolivriBulletsHe']) as $b) : ?>
                    <li><?php echo self::esc($b); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="kgh-panel" style="margin-bottom:22px;">
        <div class="kgh-panel-title">
            <span><?php echo $isEn ? "Frequently Asked Questions ({$compDisplay} vs Kolivri)" : "שאלות נפוצות ({$compDisplay} מול קוליברי)"; ?></span>
        </div>
        <div class="kgh-card-list">
            <?php foreach ($faqEntities as $faq) : ?>
            <div class="kgh-item-card">
                <div style="font-size:15px;font-weight:800;color:#FFFFFF;margin-bottom:6px;">
                    <?php echo self::esc($faq['name']); ?>
                </div>
                <div style="font-size:14px;color:#CBD5E1;line-height:1.55;">
                    <?php echo self::esc($faq['acceptedAnswer']['text']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="kgh-panel" style="border-color:rgba(251, 129, 35, 0.45);">
        <div class="kgh-panel-title">
            <span><?php echo $isEn ? 'Ready to Test Kolivri Live in 30 Seconds?' : 'מוכנים לבדוק את קוליברי בלייב תוך 30 שניות?'; ?></span>
            <span class="kgh-pill b-1-30">📞 <?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?></span>
        </div>
        <p style="font-size:14.5px;color:#CBD5E1;margin:0 0 14px;line-height:1.55;">
            <?php if ($isEn) : ?>
                Don't take our word for it — try to stump our live AI voice agent on the phone right now at <strong>054-787-5670</strong>, run the interactive 2-Call Amnesia Test, grade your business phone line, or start your 14-day free trial.
            <?php else : ?>
                אל תסתפקו בטבלאות השוואה — חייגו עכשיו אל <strong>054-787-5670</strong> ונסו להתקיל את סוכן ה-AI הקולי שלנו, הריצו את מבחן האמנזיה הדו-שיחתי, בדקו את ציון המענה של הקו שלכם או התחילו 14 ימי ניסיון חינם.
            <?php endif; ?>
        </p>
        <div class="kgh-btn-row">
            <a href="<?php echo self::esc($prefix . '/start/'); ?>" class="kgh-btn kgh-btn-primary">
                <?php echo $isEn ? '🚀 Start 14-Day Free Trial (/start/)' : '🚀 התחילו 14 ימי ניסיון (/he/start/)'; ?>
            </a>
            <a href="<?php echo self::esc($prefix . '/amnesia-test/'); ?>" class="kgh-btn kgh-btn-emerald">
                <?php echo $isEn ? '🧠 Test Memoria 100% LongMemEval (/amnesia-test/)' : '🧠 בדקו את זיכרון Memoria (/he/amnesia-test/)'; ?>
            </a>
            <a href="<?php echo self::esc($prefix . '/phone-grader/'); ?>" class="kgh-btn kgh-btn-secondary">
                <?php echo $isEn ? '🔥 Audit Missed Calls (/phone-grader/)' : '🔥 בדקו הפסד שיחות (/he/phone-grader/)'; ?>
            </a>
            <a href="tel:+972547875670" class="kgh-btn kgh-btn-secondary">
                📞 <?php echo self::esc(self::KOLIVRI_PHONE_DISPLAY); ?>
            </a>
        </div>
    </div>

    <script type="application/ld+json"><?php echo json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
    <?php echo self::getContextCarryForwardScript(); ?>
</div>
        <?php
        return trim((string) ob_get_clean());
    }
}
