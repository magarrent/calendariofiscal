<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateTaxModelDescriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $descriptions = [
            // Modelos 030-040 - Declaraciones censales
            '030' => 'Declaración censal de alta, cambio de domicilio y/o variación de datos personales',
            '031' => 'Declaración informativa mensual de cuentas de depósito y de custodia',
            '036' => 'Declaración censal de alta, modificación y baja de empresarios y profesionales',
            '037' => 'Declaración censal de alta, modificación y baja simplificada',
            '038' => 'Declaración censal de alta, modificación y baja simplificada',
            '039' => 'Declaración censal de alta, modificación y baja de entidades',
            '040' => 'Declaración censal de alta, modificación y baja simplificada de retenedores',

            // Modelos 100-136 - IRPF y Patrimonio
            '100' => 'Declaración anual del Impuesto sobre la Renta de las Personas Físicas',
            '102' => 'Impuesto sobre la Renta de las Personas Físicas. Declaración abreviada',
            '111' => 'Declaración de retenciones e ingresos a cuenta sobre rendimientos del trabajo y de actividades profesionales',
            '115' => 'Declaración de retenciones e ingresos a cuenta sobre rentas de arrendamiento de inmuebles urbanos',
            '117' => 'Declaración de retenciones e ingresos a cuenta en IRPF, Impuesto sobre Sociedades e IRNR',
            '123' => 'Declaración de retenciones e ingresos a cuenta sobre determinadas rentas',
            '124' => 'Declaración de retenciones e ingresos a cuenta sobre primas de seguros',
            '126' => 'Declaración de retenciones e ingresos a cuenta sobre rendimientos de capital mobiliario',
            '128' => 'Declaración de retenciones e ingresos a cuenta sobre ganancias o pérdidas patrimoniales',
            '130' => 'Autoliquidación de retenciones del Impuesto sobre la Renta de no Residentes',
            '131' => 'Autoliquidación de retenciones del Impuesto sobre la Renta de no Residentes. Trimestral',
            '136' => 'Declaración informativa sobre operaciones de traspaso de acciones nominativas',
            '151' => 'Declaración del Impuesto sobre el Patrimonio de las Personas Físicas',

            // Modelos 159-216 - Información y resúmenes anuales
            '159' => 'Declaración anual de consumo de energía eléctrica',
            '170' => 'Declaración anual de actividades económicas con consumo de energía eléctrica',
            '180' => 'Resumen anual de retenciones sobre rentas de arrendamiento de inmuebles urbanos',
            '188' => 'Resumen anual de retenciones sobre determinadas rentas',
            '189' => 'Declaración informativa de valores, seguros y rentas',
            '190' => 'Resumen anual de retenciones sobre rendimientos del trabajo y de actividades económicas',
            '192' => 'Declaración informativa de operaciones con activos financieros y valores',
            '193' => 'Resumen anual de retenciones sobre determinadas rentas financieras',
            '194' => 'Resumen anual de retenciones sobre capital mobiliario y ganancias patrimoniales',
            '196' => 'Declaración informativa de operaciones con compraventas de valores',
            '198' => 'Declaración informativa de seguros y planes de pensiones',
            '200' => 'Declaración del Impuesto sobre Sociedades. Modelo abreviado',
            '202' => 'Autoliquidación de retenciones del Impuesto sobre Sociedades',
            '206' => 'Declaración del Impuesto sobre Sociedades. Modelo simplificado',
            '210' => 'Declaración de retenciones e ingresos a cuenta del Impuesto sobre Sociedades',
            '213' => 'Resumen anual de retenciones sobre rendimientos de personas no residentes',
            '216' => 'Declaración de retenciones e ingresos a cuenta sobre rendimientos de capital inmobiliario',

            // Modelos 220-296 - IVA y Declaraciones informativas
            '220' => 'Declaración del Impuesto sobre Sociedades. Sociedades de régimen simplificado',
            '221' => 'Autoliquidación del Impuesto Complementario de Sociedades',
            '222' => 'Autoliquidación de retenciones del Impuesto sobre Sociedades. Trimestral',
            '230' => 'Declaración de retenciones e ingresos a cuenta sobre primas de seguros',
            '232' => 'Declaración informativa mensual de cuentas de depósito y de custodia',
            '235' => 'Declaración informativa mensual de cuentas de depósito y de custodia',
            '236' => 'Declaración informativa mensual de cuentas corrientes en toda clase de monedas',
            '240' => 'Declaración informativa sobre pagos transfronterizos y pérdidas fiscales',
            '241' => 'Declaración informativa sobre pagos transfronterizos',
            '242' => 'Autoliquidación del Impuesto Complementario sobre Operaciones Empresariales',
            '270' => 'Resumen anual de retenciones sobre rendimientos de trabajo y actividades económicas de no residentes',
            '280' => 'Declaración informativa mensual de cuentas corrientes en toda clase de monedas',
            '281' => 'Declaración informativa mensual de cuentas corrientes en toda clase de monedas',
            '282' => 'Declaración del Impuesto sobre la Renta de no Residentes sin establecimiento permanente',
            '283' => 'Resumen anual de retenciones sobre rendimientos de personas no residentes sin establecimiento',
            '289' => 'Declaración informativa mensual de cuentas corrientes',
            '290' => 'Declaración informativa mensual de cuentas de depósito',
            '291' => 'Declaración informativa de cuentas de no residentes',
            '294' => 'Declaración informativa de obligaciones de tenencia de registros e identificación',
            '295' => 'Declaración informativa de fondos de instituciones de inversión colectiva',
            '296' => 'Resumen anual de rendimientos de personas no residentes',

            // Modelos 303-390 - IVA
            '303' => 'Declaración-Liquidación del Impuesto sobre el Valor Añadido',
            '308' => 'Declaración del IVA. Régimen de clientes',
            '309' => 'Declaración-Liquidación no periódica del Impuesto sobre el Valor Añadido',
            '318' => 'Declaración del IVA. Régimen simplificado de caja',
            '322' => 'Declaración-Liquidación mensual del IVA. Grupos de entidades',
            '341' => 'Resumen anual de IVA. Intracomunitario',
            '345' => 'Declaración informativa de cuentas de no residentes',
            '346' => 'Resumen anual del IVA. Modelo de resumen anual',
            '347' => 'Declaración anual de operaciones con terceras personas',
            '349' => 'Declaración-Liquidación del IVA. Entidades con régimen especial',
            '353' => 'Declaración-Liquidación del IVA. Régimen de cuota tributaria mínima',
            '360' => 'Declaración-Liquidación anual del IVA',
            '361' => 'Resumen anual del IVA para personas no residentes',
            '362' => 'Declaración-Liquidación trimestral del IVA. Grupos de entidades',
            '363' => 'Declaración del Impuesto sobre el Valor Añadido. Régimen especial de empresas innovadoras',
            '364' => 'Declaración-Liquidación trimestral del IVA. Régimen especial del comercio minorista',
            '365' => 'Declaración-Liquidación trimestral del IVA. Régimen especial de agricultura, ganadería y pesca',
            '368' => 'Declaración-Liquidación del IVA. Régimen especial de las actuaciones de vivienda',
            '369' => 'Declaración-Liquidación del IVA. Solicitud de devolución de saldos de IVA positivos',
            '379' => 'Declaración informativa sobre pagos transfronterizos y pérdidas fiscales',
            '380' => 'Declaración-Liquidación mensual del IVA',
            '381' => 'Declaración-Liquidación trimestral del IVA',
            '390' => 'Resumen anual del IVA',

            // Modelos 410-490 - Otros impuestos
            '410' => 'Impuesto sobre Depósitos en las Entidades de Crédito. Autoliquidación',
            '411' => 'Impuesto sobre Depósitos en las Entidades de Crédito. Solicitud de devolución',
            '430' => 'Declaración-Liquidación del Impuesto sobre las Primas de Seguros',
            '480' => 'Resumen anual del Impuesto sobre las Primas de Seguros',
            '490' => 'Autoliquidación del Impuesto sobre Determinados Servicios Digitales',

            // Modelos 506-604 - Impuestos especiales y medioambientales
            '506' => 'Declaración de Impuestos Especiales. Alcohol y bebidas derivadas',
            '507' => 'Declaración de Impuestos Especiales. Productos intermedios y alcohol etílico',
            '508' => 'Declaración de Impuestos Especiales. Hidrocarburos',
            '512' => 'Declaración anual del Impuesto sobre Hidrocarburos',
            '521' => 'Declaración de Impuestos Especiales. Labores de tabaco',
            '522' => 'Declaración de Impuestos Especiales. Gasóleo profesional',
            '524' => 'Declaración de Impuestos Especiales. Energía eléctrica',
            '535' => 'Calendario de fechas de campaña de presentación de declaraciones de IRPF y Patrimonio',
            '547' => 'Declaración de Impuestos Especiales. Consumo de energía eléctrica',
            '548' => 'Declaración de Impuestos Especiales. Desgravación de combustibles',
            '553' => 'Declaración de Impuestos Especiales. Efectos especiales',
            '554' => 'Registro de operaciones de depósito aduanal. Impuestos especiales',
            '555' => 'Registro de operaciones de tenedores de impuestos especiales',
            '556' => 'Registro de cuentas de distribución. Impuestos especiales',
            '557' => 'Registro de cambios y pérdidas. Impuestos especiales',
            '558' => 'Registro de operaciones comerciales. Impuestos especiales',
            '560' => 'Declaración-Liquidación del Impuesto Especial sobre la Electricidad',
            '561' => 'Declaración de Impuestos Especiales. Combustibles de aviación',
            '562' => 'Declaración de Impuestos Especiales. Gasóleos de uso profesional',
            '563' => 'Declaración de Impuestos Especiales. Energía eléctrica consumida',
            '566' => 'Declaración de Impuestos Especiales. Emisiones de CO2',
            '570' => 'Registro de existencias. Impuestos especiales',
            '572' => 'Declaración de Impuestos Especiales. Alcohol y bebidas alcohólicas',
            '573' => 'Declaración de Impuestos Especiales. Minuta de despacho',
            '580' => 'Registro de entrada de productos. Impuestos especiales',
            '581' => 'Declaración de Impuestos Especiales. Salida de productos',
            '583' => 'Declaración anual de Impuestos Medioambientales',
            '584' => 'Declaración anual del Impuesto sobre Contaminación Atmosférica',
            '585' => 'Declaración anual del Impuesto sobre Residuos de Aparatos Eléctricos y Electrónicos',
            '587' => 'Declaración trimestral de Impuestos Medioambientales',
            '589' => 'Declaración anual del Impuesto sobre Envases de Plástico no Reutilizable',
            '591' => 'Declaración anual del Impuesto sobre Bebidas con Contenido de Azúcares Añadidos',
            '592' => 'Declaración-Liquidación mensual del Impuesto Especial sobre la Electricidad',
            '593' => 'Declaración trimestral de Impuestos Medioambientales',
            '595' => 'Declaración trimestral del Impuesto Especial sobre el Carbón',
            '596' => 'Declaración anual del Impuesto Especial sobre el Carbón',
            '604' => 'Declaración-Liquidación mensual del Impuesto sobre las Transacciones Financieras',

            // Modelos 650-840 - IRPF e IAE
            '650' => 'Solicitud de devolución de IRPF',
            '651' => 'Solicitud de devolución de IRPF. Régimen simplificado',
            '655' => 'Declaración del IRPF mediante opción simplificada',
            '714' => 'Declaración del Impuesto sobre el Patrimonio. Personas no residentes',
            '718' => 'Declaración del Impuesto temporal de Solidaridad de las Grandes Fortunas',
            '720' => 'Declaración del Impuesto sobre Sucesiones y Donaciones',
            '721' => 'Declaración del Impuesto sobre Sucesiones y Donaciones. Opción simplificada',
            '780' => 'Resumen anual del Impuesto sobre las Transacciones Financieras',
            '781' => 'Declaración del Impuesto sobre las Transacciones Financieras',
            '792' => 'Resumen anual de operaciones con terceras personas',
            '793' => 'Declaración del Impuesto sobre las Transacciones Financieras. Entidades de crédito',
            '840' => 'Impuesto sobre Actividades Económicas. Declaración anual',
            '901' => 'Notificación de fechas de campaña de IRPF y Patrimonio',
        ];

        foreach ($descriptions as $modelNumber => $description) {
            \App\Models\TaxModel::where('model_number', $modelNumber)
                ->update(['description' => $description]);
        }
    }
}
