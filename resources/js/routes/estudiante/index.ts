import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import acceso5cb403 from './acceso'
import cuenta from './cuenta'
import expedientes from './expedientes'
/**
* @see \App\Http\Controllers\Estudiante\AccesoController::acceso
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
export const acceso = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: acceso.url(options),
    method: 'get',
})

acceso.definition = {
    methods: ["get","head"],
    url: '/estudiante/acceso',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::acceso
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
acceso.url = (options?: RouteQueryOptions) => {
    return acceso.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::acceso
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
acceso.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: acceso.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::acceso
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
acceso.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: acceso.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::acceso
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
const accesoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: acceso.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::acceso
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
accesoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: acceso.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::acceso
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
accesoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: acceso.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

acceso.form = accesoForm

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::carreras
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
export const carreras = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: carreras.url(options),
    method: 'get',
})

carreras.definition = {
    methods: ["get","head"],
    url: '/estudiante/carreras',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::carreras
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
carreras.url = (options?: RouteQueryOptions) => {
    return carreras.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::carreras
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
carreras.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: carreras.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::carreras
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
carreras.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: carreras.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::carreras
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
const carrerasForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: carreras.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::carreras
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
carrerasForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: carreras.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::carreras
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
carrerasForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: carreras.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

carreras.form = carrerasForm

const estudiante = {
    acceso: Object.assign(acceso, acceso5cb403),
    cuenta: Object.assign(cuenta, cuenta),
    carreras: Object.assign(carreras, carreras),
    expedientes: Object.assign(expedientes, expedientes),
}

export default estudiante