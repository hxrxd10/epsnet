import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import departamentos from './departamentos'
import municipios from './municipios'
import unidades from './unidades'
import usuarios from './usuarios'
import catalogos from './catalogos'
/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::datos
* @see app/Http/Controllers/Admin/ManejoDatosController.php:19
* @route '/admin/datos'
*/
export const datos = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: datos.url(options),
    method: 'get',
})

datos.definition = {
    methods: ["get","head"],
    url: '/admin/datos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::datos
* @see app/Http/Controllers/Admin/ManejoDatosController.php:19
* @route '/admin/datos'
*/
datos.url = (options?: RouteQueryOptions) => {
    return datos.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::datos
* @see app/Http/Controllers/Admin/ManejoDatosController.php:19
* @route '/admin/datos'
*/
datos.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: datos.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::datos
* @see app/Http/Controllers/Admin/ManejoDatosController.php:19
* @route '/admin/datos'
*/
datos.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: datos.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::datos
* @see app/Http/Controllers/Admin/ManejoDatosController.php:19
* @route '/admin/datos'
*/
const datosForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: datos.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::datos
* @see app/Http/Controllers/Admin/ManejoDatosController.php:19
* @route '/admin/datos'
*/
datosForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: datos.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::datos
* @see app/Http/Controllers/Admin/ManejoDatosController.php:19
* @route '/admin/datos'
*/
datosForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: datos.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

datos.form = datosForm

const admin = {
    datos: Object.assign(datos, datos),
    departamentos: Object.assign(departamentos, departamentos),
    municipios: Object.assign(municipios, municipios),
    unidades: Object.assign(unidades, unidades),
    usuarios: Object.assign(usuarios, usuarios),
    catalogos: Object.assign(catalogos, catalogos),
}

export default admin