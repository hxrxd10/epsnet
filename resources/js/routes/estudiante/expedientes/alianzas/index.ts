import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::store
* @see app/Http/Controllers/Estudiante/AlianzaController.php:16
* @route '/estudiante/expedientes/{expediente}/alianzas'
*/
export const store = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/expedientes/{expediente}/alianzas',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::store
* @see app/Http/Controllers/Estudiante/AlianzaController.php:16
* @route '/estudiante/expedientes/{expediente}/alianzas'
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
* @see \App\Http\Controllers\Estudiante\AlianzaController::store
* @see app/Http/Controllers/Estudiante/AlianzaController.php:16
* @route '/estudiante/expedientes/{expediente}/alianzas'
*/
store.post = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::store
* @see app/Http/Controllers/Estudiante/AlianzaController.php:16
* @route '/estudiante/expedientes/{expediente}/alianzas'
*/
const storeForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::store
* @see app/Http/Controllers/Estudiante/AlianzaController.php:16
* @route '/estudiante/expedientes/{expediente}/alianzas'
*/
storeForm.post = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::update
* @see app/Http/Controllers/Estudiante/AlianzaController.php:23
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
*/
export const update = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/estudiante/expedientes/{expediente}/alianzas/{registro}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::update
* @see app/Http/Controllers/Estudiante/AlianzaController.php:23
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
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
* @see \App\Http\Controllers\Estudiante\AlianzaController::update
* @see app/Http/Controllers/Estudiante/AlianzaController.php:23
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
*/
update.put = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::update
* @see app/Http/Controllers/Estudiante/AlianzaController.php:23
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
*/
update.patch = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::update
* @see app/Http/Controllers/Estudiante/AlianzaController.php:23
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
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
* @see \App\Http\Controllers\Estudiante\AlianzaController::update
* @see app/Http/Controllers/Estudiante/AlianzaController.php:23
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
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
* @see \App\Http\Controllers\Estudiante\AlianzaController::update
* @see app/Http/Controllers/Estudiante/AlianzaController.php:23
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
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
* @see \App\Http\Controllers\Estudiante\AlianzaController::destroy
* @see app/Http/Controllers/Estudiante/AlianzaController.php:30
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
*/
export const destroy = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/estudiante/expedientes/{expediente}/alianzas/{registro}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::destroy
* @see app/Http/Controllers/Estudiante/AlianzaController.php:30
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
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
* @see \App\Http\Controllers\Estudiante\AlianzaController::destroy
* @see app/Http/Controllers/Estudiante/AlianzaController.php:30
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
*/
destroy.delete = (args: { expediente: number | { id: number }, registro: string | number } | [expediente: number | { id: number }, registro: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Estudiante\AlianzaController::destroy
* @see app/Http/Controllers/Estudiante/AlianzaController.php:30
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
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
* @see \App\Http\Controllers\Estudiante\AlianzaController::destroy
* @see app/Http/Controllers/Estudiante/AlianzaController.php:30
* @route '/estudiante/expedientes/{expediente}/alianzas/{registro}'
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

const alianzas = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default alianzas