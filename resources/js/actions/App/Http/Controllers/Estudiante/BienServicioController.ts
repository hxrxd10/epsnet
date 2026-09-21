import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::index
* @see app/Http/Controllers/Estudiante/BienServicioController.php:21
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
export const index = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/estudiante/expedientes/{expediente}/bienes-servicios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::index
* @see app/Http/Controllers/Estudiante/BienServicioController.php:21
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
index.url = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { expediente: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { expediente: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            expediente: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        expediente: typeof args.expediente === 'object'
        ? args.expediente.id
        : args.expediente,
    }

    return index.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::index
* @see app/Http/Controllers/Estudiante/BienServicioController.php:21
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
index.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::index
* @see app/Http/Controllers/Estudiante/BienServicioController.php:21
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
index.head = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::index
* @see app/Http/Controllers/Estudiante/BienServicioController.php:21
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
const indexForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::index
* @see app/Http/Controllers/Estudiante/BienServicioController.php:21
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
indexForm.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::index
* @see app/Http/Controllers/Estudiante/BienServicioController.php:21
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
indexForm.head = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::store
* @see app/Http/Controllers/Estudiante/BienServicioController.php:43
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
export const store = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/expedientes/{expediente}/bienes-servicios',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::store
* @see app/Http/Controllers/Estudiante/BienServicioController.php:43
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
store.url = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { expediente: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { expediente: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            expediente: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        expediente: typeof args.expediente === 'object'
        ? args.expediente.id
        : args.expediente,
    }

    return store.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::store
* @see app/Http/Controllers/Estudiante/BienServicioController.php:43
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
store.post = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::store
* @see app/Http/Controllers/Estudiante/BienServicioController.php:43
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
const storeForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::store
* @see app/Http/Controllers/Estudiante/BienServicioController.php:43
* @route '/estudiante/expedientes/{expediente}/bienes-servicios'
*/
storeForm.post = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::update
* @see app/Http/Controllers/Estudiante/BienServicioController.php:50
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
export const update = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::update
* @see app/Http/Controllers/Estudiante/BienServicioController.php:50
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
update.url = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            expediente: args[0],
            registro: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        expediente: typeof args.expediente === 'object'
        ? args.expediente.id
        : args.expediente,
        registro: args.registro,
    }

    return update.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace('{registro}', parsedArgs.registro.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::update
* @see app/Http/Controllers/Estudiante/BienServicioController.php:50
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
update.put = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::update
* @see app/Http/Controllers/Estudiante/BienServicioController.php:50
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
update.patch = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::update
* @see app/Http/Controllers/Estudiante/BienServicioController.php:50
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
const updateForm = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::update
* @see app/Http/Controllers/Estudiante/BienServicioController.php:50
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
updateForm.put = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::update
* @see app/Http/Controllers/Estudiante/BienServicioController.php:50
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
updateForm.patch = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::destroy
* @see app/Http/Controllers/Estudiante/BienServicioController.php:57
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
export const destroy = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::destroy
* @see app/Http/Controllers/Estudiante/BienServicioController.php:57
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
destroy.url = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            expediente: args[0],
            registro: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        expediente: typeof args.expediente === 'object'
        ? args.expediente.id
        : args.expediente,
        registro: args.registro,
    }

    return destroy.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace('{registro}', parsedArgs.registro.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::destroy
* @see app/Http/Controllers/Estudiante/BienServicioController.php:57
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
destroy.delete = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::destroy
* @see app/Http/Controllers/Estudiante/BienServicioController.php:57
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
const destroyForm = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\BienServicioController::destroy
* @see app/Http/Controllers/Estudiante/BienServicioController.php:57
* @route '/estudiante/expedientes/{expediente}/bienes-servicios/{registro}'
*/
destroyForm.delete = (args: { expediente: string | number | { id: string | number }, registro: string | number } | [expediente: string | number | { id: string | number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const BienServicioController = { index, store, update, destroy }

export default BienServicioController