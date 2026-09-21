import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::index
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:19
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
export const index = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/estudiante/expedientes/{expediente}/publicaciones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::index
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:19
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
index.url = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::index
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:19
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
index.get = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::index
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:19
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
index.head = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::index
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:19
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
const indexForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::index
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:19
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
indexForm.get = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::index
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:19
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
indexForm.head = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::store
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:41
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
export const store = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/expedientes/{expediente}/publicaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::store
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:41
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
store.url = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::store
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:41
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
store.post = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::store
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:41
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
const storeForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::store
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:41
* @route '/estudiante/expedientes/{expediente}/publicaciones'
*/
storeForm.post = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::update
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:48
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
export const update = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/estudiante/expedientes/{expediente}/publicaciones/{registro}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::update
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:48
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
update.url = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::update
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:48
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
update.put = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::update
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:48
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
update.patch = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::update
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:48
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
const updateForm = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::update
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:48
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
updateForm.put = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::update
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:48
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
updateForm.patch = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::destroy
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:55
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
export const destroy = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/estudiante/expedientes/{expediente}/publicaciones/{registro}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::destroy
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:55
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
destroy.url = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::destroy
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:55
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
destroy.delete = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::destroy
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:55
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
const destroyForm = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\PublicacionInvestigacionController::destroy
* @see app/Http/Controllers/Estudiante/PublicacionInvestigacionController.php:55
* @route '/estudiante/expedientes/{expediente}/publicaciones/{registro}'
*/
destroyForm.delete = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const PublicacionInvestigacionController = { index, store, update, destroy }

export default PublicacionInvestigacionController