<?php
/**
 * pasos.php — Definición declarativa de los pasos del wizard de apertura.
 *
 * Los campos de Persona Física están tomados TEXTUALMENTE del
 * "Formulario de Registro de Cliente Persona Física" de Valores
 * (ver includes/kyc-referencia/). La rama Jurídica es provisional
 * (set inferido) hasta contar con su KYC propio. La Cuenta Conjunta
 * reutiliza los datos de Física + un anexo de titulares adicionales
 * (según indica el propio formulario: "Anexo KYC 2").
 *
 * tipos de campo: text|email|tel|number|date|select|radio|checkbox|textarea|
 *                 group_money|repeater
 */

declare(strict_types=1);

/** Rangos de volumen anual (idénticos al formulario). */
function apertura_rangos_volumen(): array
{
    return [
        'Inferior a USD 10.000',
        'Entre USD 10.000 y USD 100.000',
        'Entre USD 100.000 y USD 500.000',
        'Superior a USD 500.000',
    ];
}

/** Pasos de Persona Física. Cada paso: clave, titulo, campos[]. */
function apertura_pasos_fisica(): array
{
    return [
        [
            'clave' => 'datos_personales', 'titulo' => 'Datos personales',
            'campos' => [
                ['n'=>'nombres','l'=>'Nombres y Apellidos','t'=>'text','req'=>true],
                ['n'=>'fecha_nacimiento','l'=>'Fecha de nacimiento','t'=>'date'],
                ['n'=>'sexo','l'=>'Sexo','t'=>'select','op'=>['M'=>'Masculino','F'=>'Femenino']],
                ['n'=>'documento','l'=>'N° de documento','t'=>'text','req'=>true],
                ['n'=>'ruc','l'=>'RUC','t'=>'text'],
                ['n'=>'nacionalidad','l'=>'Nacionalidad','t'=>'text'],
                ['n'=>'estado_civil','l'=>'Estado civil','t'=>'select','op'=>['soltero'=>'Soltero','casado'=>'Casado','viudo'=>'Viudo','divorciado'=>'Divorciado']],
                ['n'=>'separacion_bienes','l'=>'Separación de bienes','t'=>'radio','op'=>['si'=>'Sí','no'=>'No']],
                ['n'=>'conyuge_nombres','l'=>'Nombres y apellidos del cónyuge','t'=>'text'],
                ['n'=>'conyuge_documento','l'=>'N° de documento del cónyuge','t'=>'text'],
            ],
        ],
        [
            'clave' => 'domicilio', 'titulo' => 'Domicilio y contacto',
            'campos' => [
                ['n'=>'dom_calle1','l'=>'Calle I','t'=>'text','req'=>true],
                ['n'=>'dom_numero','l'=>'N° de domicilio','t'=>'text'],
                ['n'=>'dom_calle2','l'=>'Calle II','t'=>'text'],
                ['n'=>'dom_edificio','l'=>'Edificio','t'=>'text'],
                ['n'=>'dom_ciudad','l'=>'Ciudad','t'=>'text','req'=>true],
                ['n'=>'dom_barrio','l'=>'Barrio','t'=>'text'],
                ['n'=>'dom_departamento','l'=>'Departamento','t'=>'text'],
                ['n'=>'dom_pais','l'=>'País de residencia','t'=>'text'],
                ['n'=>'dom_linea_baja','l'=>'Línea baja','t'=>'tel'],
                ['n'=>'celular','l'=>'Celular','t'=>'tel','req'=>true],
                ['n'=>'email','l'=>'Correo electrónico','t'=>'email','req'=>true],
            ],
        ],
        [
            'clave' => 'laborales', 'titulo' => 'Datos laborales',
            'campos' => [
                ['n'=>'situacion_laboral','l'=>'Situación laboral','t'=>'select','op'=>['asalariado'=>'Asalariado','unipersonal'=>'Unipersonal','independiente'=>'Independiente','jubilado'=>'Jubilado','ama_casa'=>'Ama de casa','otros'=>'Otros']],
                ['n'=>'empresa','l'=>'Nombre de la empresa','t'=>'text'],
                ['n'=>'actividad_economica','l'=>'Actividad económica','t'=>'text'],
                ['n'=>'cargo','l'=>'Cargo','t'=>'text'],
                ['n'=>'antiguedad','l'=>'Antigüedad','t'=>'text'],
                ['n'=>'lab_email','l'=>'Correo electrónico laboral','t'=>'email'],
            ],
        ],
        [
            'clave' => 'patrimonio', 'titulo' => 'Situación patrimonial',
            'campos' => [
                ['n'=>'moneda_ref','l'=>'Moneda de referencia','t'=>'select','op'=>['Gs'=>'Guaraníes','USD'=>'Dólares']],
                ['n'=>'ingresos_mensuales','l'=>'Total ingresos mensuales','t'=>'number'],
                ['n'=>'egresos_mensuales','l'=>'Total egresos mensuales','t'=>'number'],
                ['n'=>'total_activos','l'=>'Total activos','t'=>'number'],
                ['n'=>'total_pasivos','l'=>'Total pasivos','t'=>'number'],
                ['n'=>'patrimonio_neto','l'=>'Patrimonio neto','t'=>'number'],
                ['n'=>'volumen_anual','l'=>'Volumen de movimiento esperado anualmente','t'=>'select','op'=>apertura_rangos_volumen()],
            ],
        ],
        [
            'clave' => 'bancarios', 'titulo' => 'Datos bancarios',
            'campos' => [
                ['n'=>'gs_titular','l'=>'Cuenta en Guaraníes — Titular','t'=>'text'],
                ['n'=>'gs_tipo','l'=>'Tipo (Gs.)','t'=>'select','op'=>['corriente'=>'Cuenta corriente','ahorro'=>'Caja de ahorro']],
                ['n'=>'gs_numero','l'=>'N° de cuenta (Gs.)','t'=>'text'],
                ['n'=>'gs_entidad','l'=>'Entidad (Gs.)','t'=>'text'],
                ['n'=>'usd_titular','l'=>'Cuenta en Dólares — Titular','t'=>'text'],
                ['n'=>'usd_tipo','l'=>'Tipo (USD)','t'=>'select','op'=>['corriente'=>'Cuenta corriente','ahorro'=>'Caja de ahorro']],
                ['n'=>'usd_numero','l'=>'N° de cuenta (USD)','t'=>'text'],
                ['n'=>'usd_entidad','l'=>'Entidad (USD)','t'=>'text'],
            ],
        ],
        [
            'clave' => 'operaciones', 'titulo' => 'Operaciones',
            'campos' => [
                ['n'=>'origen_fondos','l'=>'Origen de los fondos','t'=>'select','op'=>['ahorro'=>'Ahorro','venta_propiedad'=>'Venta de propiedad','inversiones'=>'Inversiones','dividendos'=>'Dividendos','jubilacion'=>'Jubilación','salario'=>'Salario','otros'=>'Otros']],
                ['n'=>'proposito','l'=>'Propósito de la relación con Valores','t'=>'textarea'],
                ['n'=>'vinculado','l'=>'¿Se encuentra vinculado a la Casa de Bolsa?','t'=>'radio','op'=>['si'=>'Sí','no'=>'No']],
                ['n'=>'vinculado_motivo','l'=>'Motivo de la vinculación','t'=>'textarea'],
                ['n'=>'tipo_orden','l'=>'Tipo de orden que la Casa de Bolsa recibirá','t'=>'select','op'=>['escrita'=>'Escrita','email'=>'Vía e-mail (solo del correo declarado)']],
            ],
        ],
        [
            'clave' => 'pep', 'titulo' => 'PEP (Persona Expuesta Políticamente)',
            'campos' => [
                ['n'=>'pep_es','l'=>'¿Ocupa o ha ocupado cargos públicos relevantes (Res. SEPRELAD 50/19)?','t'=>'radio','op'=>['si'=>'Sí','no'=>'No'],'req'=>true],
                ['n'=>'pep_detalle','l'=>'Detalle (país, cargo, institución y período)','t'=>'textarea'],
                ['n'=>'pep_relacion','l'=>'¿Tiene relación (2° grado, cónyuge/conviviente) con un PEP?','t'=>'radio','op'=>['si'=>'Sí','no'=>'No'],'req'=>true],
                ['n'=>'pep_relacion_detalle','l'=>'Detalle de la relación con PEP','t'=>'textarea'],
            ],
        ],
        [
            'clave' => 'fatca', 'titulo' => 'Ley FATCA',
            'campos' => [
                ['n'=>'fatca_sujeto','l'=>'¿Está sujeto a la Ley FATCA?','t'=>'radio','op'=>['si'=>'Sí','no'=>'No'],'req'=>true],
                ['n'=>'fatca_ciudadano','l'=>'¿Es ciudadano/residente de EE.UU.?','t'=>'radio','op'=>['si'=>'Sí','no'=>'No']],
                ['n'=>'fatca_residente_fiscal','l'=>'¿Es residente fiscal de EE.UU. (Green Card o permanencia)?','t'=>'radio','op'=>['si'=>'Sí','no'=>'No']],
                ['n'=>'fatca_autoriza','l'=>'¿Autoriza a Valores a enviar información a EE.UU. bajo FATCA?','t'=>'radio','op'=>['si'=>'Sí','no'=>'No']],
            ],
        ],
        [
            'clave' => 'declaracion', 'titulo' => 'Declaración jurada',
            'campos' => [
                ['n'=>'acepta_informconf','l'=>'Autorizo la consulta a INFORMCONF y CRITERION, y la búsqueda en listas de sanciones.','t'=>'checkbox','req'=>true],
                ['n'=>'acepta_seprelad','l'=>'Declaro bajo juramento (Seprelad) que los fondos provienen de actividades lícitas y que los datos son verdaderos.','t'=>'checkbox','req'=>true],
            ],
        ],
    ];
}

/**
 * Pasos de Persona Jurídica (PROVISIONAL — set inferido del prompt).
 * ⚠️ Ajustar cuando Valores entregue el KYC de Persona Jurídica.
 */
function apertura_pasos_juridica(): array
{
    return [
        [
            'clave' => 'sociedad', 'titulo' => 'Datos de la sociedad',
            'campos' => [
                ['n'=>'razon_social','l'=>'Razón social','t'=>'text','req'=>true],
                ['n'=>'tipo_sociedad','l'=>'Tipo de sociedad','t'=>'select','op'=>['sa'=>'S.A.','sae'=>'S.A.E.','saeca'=>'S.A.E.C.A.','srl'=>'S.R.L.','coop'=>'Cooperativa','osfl'=>'OSFL','simple'=>'Sociedad Simple','otro'=>'Otro']],
                ['n'=>'ruc','l'=>'RUC','t'=>'text','req'=>true],
                ['n'=>'fecha_constitucion','l'=>'Fecha de constitución','t'=>'date'],
                ['n'=>'actividad_economica','l'=>'Actividad económica','t'=>'text'],
                ['n'=>'dom_calle1','l'=>'Domicilio — Calle I','t'=>'text'],
                ['n'=>'dom_ciudad','l'=>'Ciudad','t'=>'text'],
                ['n'=>'email','l'=>'Correo electrónico','t'=>'email','req'=>true],
                ['n'=>'celular','l'=>'Teléfono/Celular','t'=>'tel'],
                ['n'=>'sitio_web','l'=>'Sitio web','t'=>'text'],
            ],
        ],
        [
            'clave' => 'accionistas', 'titulo' => 'Accionistas/socios (≥10%)',
            'repeater' => true, 'min' => 0, 'max' => 10,
            'campos' => [
                ['n'=>'nombre','l'=>'Nombre y apellido','t'=>'text'],
                ['n'=>'documento','l'=>'Tipo y N° de documento','t'=>'text'],
                ['n'=>'domicilio','l'=>'Domicilio','t'=>'text'],
                ['n'=>'pais','l'=>'País de residencia','t'=>'text'],
                ['n'=>'participacion','l'=>'% de participación','t'=>'number'],
            ],
        ],
        [
            'clave' => 'firmantes', 'titulo' => 'Firmantes / representantes legales',
            'repeater' => true, 'min' => 1, 'max' => 10,
            'campos' => [
                ['n'=>'nombre','l'=>'Nombres y apellidos','t'=>'text','req'=>true],
                ['n'=>'documento','l'=>'N° documento','t'=>'text'],
                ['n'=>'ruc','l'=>'RUC','t'=>'text'],
                ['n'=>'nacionalidad','l'=>'Nacionalidad','t'=>'text'],
                ['n'=>'fatca','l'=>'FATCA','t'=>'radio','op'=>['si'=>'Sí','no'=>'No']],
                ['n'=>'pep','l'=>'PEP','t'=>'radio','op'=>['si'=>'Sí','no'=>'No']],
            ],
        ],
        [
            'clave' => 'patrimonio', 'titulo' => 'Situación patrimonial',
            'campos' => [
                ['n'=>'moneda_ref','l'=>'Moneda','t'=>'select','op'=>['Gs'=>'Guaraníes','USD'=>'Dólares']],
                ['n'=>'total_activos','l'=>'Total activos','t'=>'number'],
                ['n'=>'total_pasivos','l'=>'Total pasivos','t'=>'number'],
                ['n'=>'patrimonio_neto','l'=>'Patrimonio neto','t'=>'number'],
                ['n'=>'volumen_anual','l'=>'Volumen esperado anual','t'=>'select','op'=>apertura_rangos_volumen()],
            ],
        ],
        [
            'clave' => 'declaracion', 'titulo' => 'Declaración jurada',
            'campos' => [
                ['n'=>'acepta_seprelad','l'=>'Declaro bajo juramento (Seprelad) que los fondos provienen de actividades lícitas y que los datos son verdaderos.','t'=>'checkbox','req'=>true],
            ],
        ],
    ];
}

/** Devuelve los pasos según el tipo de persona. */
function apertura_pasos(string $tipo): array
{
    return $tipo === 'juridica' ? apertura_pasos_juridica() : apertura_pasos_fisica();
}

/** Índice campo=>etiqueta (para render legible del detalle en el CMS). */
function apertura_etiquetas(string $tipo): array
{
    $map = [];
    foreach (apertura_pasos($tipo) as $paso) {
        foreach ($paso['campos'] as $c) {
            $map[$c['n']] = $c['l'];
        }
    }
    return $map;
}
