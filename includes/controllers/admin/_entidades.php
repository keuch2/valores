<?php
/**
 * _entidades.php — Definición declarativa de los formularios de cada entidad
 * de contenido. El controlador genérico 'contenido' usa esto para render+guardar.
 *
 * Cada campo: ['nombre','etiqueta','tipo', ...opciones].
 *   tipo: text|textarea|richtext|select|number|date|checkbox|media|slug_auto
 */

declare(strict_types=1);

function entidades_config(): array
{
    return [
        'noticias' => [
            'titulo_singular' => 'Noticia',
            'titulo_plural'   => 'Noticias',
            'columnas_lista'  => ['titulo' => 'Título', 'categoria' => 'Categoría', 'estado' => 'Estado'],
            'campos' => [
                ['nombre'=>'titulo','etiqueta'=>'Título','tipo'=>'text','requerido'=>true],
                ['nombre'=>'resumen','etiqueta'=>'Resumen','tipo'=>'textarea'],
                ['nombre'=>'contenido','etiqueta'=>'Contenido','tipo'=>'richtext'],
                ['nombre'=>'imagen_destacada_id','etiqueta'=>'Imagen destacada','tipo'=>'media','media_tipo'=>'imagen'],
                ['nombre'=>'categoria','etiqueta'=>'Categoría','tipo'=>'select','opciones'=>['mercado'=>'Mercado','macro'=>'Macro','inter'=>'Internacional','empresa'=>'Empresa','regulacion'=>'Regulación']],
                ['nombre'=>'estado','etiqueta'=>'Estado','tipo'=>'select','opciones'=>['borrador'=>'Borrador','publicado'=>'Publicado']],
                ['nombre'=>'visible_front','etiqueta'=>'Visible en el sitio público','tipo'=>'checkbox','hint'=>'Noticias está oculto en el front por ahora; dejar desmarcado hasta activar la sección.'],
                ['nombre'=>'fecha_publicacion','etiqueta'=>'Fecha de publicación','tipo'=>'date'],
                ['nombre'=>'autor','etiqueta'=>'Autor','tipo'=>'text'],
                ['nombre'=>'seo_title','etiqueta'=>'SEO title','tipo'=>'text'],
                ['nombre'=>'seo_description','etiqueta'=>'SEO description','tipo'=>'textarea'],
            ],
        ],
        'oportunidades' => [
            'titulo_singular' => 'Oportunidad',
            'titulo_plural'   => 'Oportunidades',
            'columnas_lista'  => ['instrumento'=>'Instrumento','tipo'=>'Tipo','tasa'=>'Tasa','moneda'=>'Moneda','estado'=>'Estado'],
            'campos' => [
                ['nombre'=>'tipo','etiqueta'=>'Tipo','tipo'=>'select','opciones'=>['bono'=>'Bono','cda'=>'CDA','accion'=>'Acción','inter'=>'Internacional'],'requerido'=>true],
                ['nombre'=>'instrumento','etiqueta'=>'Instrumento','tipo'=>'text','requerido'=>true],
                ['nombre'=>'emisor','etiqueta'=>'Emisor','tipo'=>'text'],
                ['nombre'=>'tasa','etiqueta'=>'Tasa (% anual)','tipo'=>'number','step'=>'0.01'],
                ['nombre'=>'plazo','etiqueta'=>'Plazo','tipo'=>'text','hint'=>'Ej. 360 días, 2 años'],
                ['nombre'=>'moneda','etiqueta'=>'Moneda','tipo'=>'select','opciones'=>['Gs'=>'Guaraníes','USD'=>'Dólares']],
                ['nombre'=>'calificacion','etiqueta'=>'Calificación','tipo'=>'text','hint'=>'AAA, AA, A…'],
                ['nombre'=>'monto_minimo','etiqueta'=>'Monto mínimo','tipo'=>'number','step'=>'0.01'],
                ['nombre'=>'estado','etiqueta'=>'Estado','tipo'=>'select','opciones'=>['disponible'=>'Disponible','agotado'=>'Agotado']],
                ['nombre'=>'destacado','etiqueta'=>'Destacado','tipo'=>'checkbox'],
                ['nombre'=>'orden','etiqueta'=>'Orden','tipo'=>'number'],
            ],
        ],
        'servicios' => [
            'titulo_singular' => 'Servicio',
            'titulo_plural'   => 'Servicios',
            'columnas_lista'  => ['titulo'=>'Título','activo'=>'Activo'],
            'campos' => [
                ['nombre'=>'titulo','etiqueta'=>'Título','tipo'=>'text','requerido'=>true],
                ['nombre'=>'icono','etiqueta'=>'Ícono (clase)','tipo'=>'text','hint'=>'Ej. fa-building'],
                ['nombre'=>'descripcion_corta','etiqueta'=>'Descripción corta','tipo'=>'textarea'],
                ['nombre'=>'contenido','etiqueta'=>'Contenido','tipo'=>'richtext'],
                ['nombre'=>'imagen_id','etiqueta'=>'Imagen','tipo'=>'media','media_tipo'=>'imagen'],
                ['nombre'=>'orden','etiqueta'=>'Orden','tipo'=>'number'],
                ['nombre'=>'activo','etiqueta'=>'Activo','tipo'=>'checkbox'],
            ],
        ],
        'ejecutivos' => [
            'titulo_singular' => 'Ejecutivo',
            'titulo_plural'   => 'Ejecutivos',
            'columnas_lista'  => ['nombre'=>'Nombre','cargo'=>'Cargo','activo'=>'Activo'],
            'campos' => [
                ['nombre'=>'nombre','etiqueta'=>'Nombre','tipo'=>'text','requerido'=>true],
                ['nombre'=>'cargo','etiqueta'=>'Cargo','tipo'=>'text'],
                ['nombre'=>'bio','etiqueta'=>'Biografía','tipo'=>'textarea'],
                ['nombre'=>'foto_id','etiqueta'=>'Foto','tipo'=>'media','media_tipo'=>'imagen'],
                ['nombre'=>'email','etiqueta'=>'Email','tipo'=>'text'],
                ['nombre'=>'telefono','etiqueta'=>'Teléfono','tipo'=>'text'],
                ['nombre'=>'whatsapp','etiqueta'=>'WhatsApp','tipo'=>'text'],
                ['nombre'=>'linkedin','etiqueta'=>'LinkedIn (URL)','tipo'=>'text'],
                ['nombre'=>'orden','etiqueta'=>'Orden','tipo'=>'number'],
                ['nombre'=>'activo','etiqueta'=>'Activo','tipo'=>'checkbox'],
            ],
        ],
        'faqs' => [
            'titulo_singular' => 'FAQ',
            'titulo_plural'   => 'Preguntas frecuentes',
            'columnas_lista'  => ['pregunta'=>'Pregunta','categoria'=>'Categoría','activo'=>'Activo'],
            'campos' => [
                ['nombre'=>'pregunta','etiqueta'=>'Pregunta','tipo'=>'text','requerido'=>true],
                ['nombre'=>'respuesta','etiqueta'=>'Respuesta','tipo'=>'textarea','requerido'=>true],
                ['nombre'=>'categoria','etiqueta'=>'Categoría','tipo'=>'select','opciones'=>['general'=>'General','bono'=>'Bonos','cda'=>'CDAs','inter'=>'Internacional','contacto'=>'Contacto']],
                ['nombre'=>'orden','etiqueta'=>'Orden','tipo'=>'number'],
                ['nombre'=>'activo','etiqueta'=>'Activo','tipo'=>'checkbox'],
            ],
        ],
        'glosario' => [
            'titulo_singular' => 'Término',
            'titulo_plural'   => 'Glosario',
            'columnas_lista'  => ['termino'=>'Término'],
            'campos' => [
                ['nombre'=>'termino','etiqueta'=>'Término','tipo'=>'text','requerido'=>true],
                ['nombre'=>'definicion','etiqueta'=>'Definición','tipo'=>'textarea','requerido'=>true],
                ['nombre'=>'orden','etiqueta'=>'Orden','tipo'=>'number'],
            ],
        ],
        'academy' => [
            'entidad_tabla'   => 'academy_recursos',
            'titulo_singular' => 'Recurso',
            'titulo_plural'   => 'Academy',
            'columnas_lista'  => ['titulo'=>'Título','tipo'=>'Tipo','fecha'=>'Fecha','activo'=>'Activo'],
            'campos' => [
                ['nombre'=>'tipo','etiqueta'=>'Tipo','tipo'=>'select','opciones'=>['articulo'=>'Artículo','webinar'=>'Webinar'],'requerido'=>true],
                ['nombre'=>'titulo','etiqueta'=>'Título','tipo'=>'text','requerido'=>true],
                ['nombre'=>'descripcion','etiqueta'=>'Descripción','tipo'=>'textarea'],
                ['nombre'=>'video_url','etiqueta'=>'URL de video','tipo'=>'text'],
                ['nombre'=>'media_id','etiqueta'=>'Imagen/recurso','tipo'=>'media','media_tipo'=>'imagen'],
                ['nombre'=>'fecha','etiqueta'=>'Fecha','tipo'=>'date'],
                ['nombre'=>'orden','etiqueta'=>'Orden','tipo'=>'number'],
                ['nombre'=>'activo','etiqueta'=>'Activo','tipo'=>'checkbox'],
            ],
        ],
    ];
}

/** Devuelve la config de una entidad de contenido, o null. */
function entidad_cfg(string $clave): ?array
{
    return entidades_config()[$clave] ?? null;
}

/** Nombre real de la tabla (permite alias, ej. academy -> academy_recursos). */
function entidad_tabla(string $clave, array $cfg): string
{
    return $cfg['entidad_tabla'] ?? $clave;
}
