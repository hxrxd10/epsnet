import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\UsuarioController::index
* @see app/Http/Controllers/Admin/UsuarioController.php:30
* @route '/admin/usuarios'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/usuarios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\UsuarioController::index
* @see app/Http/Controllers/Admin/UsuarioController.php:30
* @route '/admin/usuarios'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\UsuarioController::index
* @see app/Http/Controllers/Admin/UsuarioController.php:30
* @route '/admin/usuarios'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::index
* @see app/Http/Controllers/Admin/UsuarioController.php:30
* @route '/admin/usuarios'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::index
* @see app/Http/Controllers/Admin/UsuarioController.php:30
* @route '/admin/usuarios'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::index
* @see app/Http/Controllers/Admin/UsuarioController.php:30
* @route '/admin/usuarios'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::index
* @see app/Http/Controllers/Admin/UsuarioController.php:30
* @route '/admin/usuarios'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Admin\UsuarioController::store
* @see app/Http/Controllers/Admin/UsuarioController.php:91
* @route '/admin/usuarios'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/usuarios',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\UsuarioController::store
* @see app/Http/Controllers/Admin/UsuarioController.php:91
* @route '/admin/usuarios'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\UsuarioController::store
* @see app/Http/Controllers/Admin/UsuarioController.php:91
* @route '/admin/usuarios'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::store
* @see app/Http/Controllers/Admin/UsuarioController.php:91
* @route '/admin/usuarios'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::store
* @see app/Http/Controllers/Admin/UsuarioController.php:91
* @route '/admin/usuarios'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\UsuarioController::update
* @see app/Http/Controllers/Admin/UsuarioController.php:117
* @route '/admin/usuarios/{usuario}'
*/
export const update = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/usuarios/{usuario}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\UsuarioController::update
* @see app/Http/Controllers/Admin/UsuarioController.php:117
* @route '/admin/usuarios/{usuario}'
*/
update.url = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { usuario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { usuario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            usuario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        usuario: typeof args.usuario === 'object'
        ? args.usuario.id
        : args.usuario,
    }

    return update.definition.url
            .replace('{usuario}', parsedArgs.usuario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\UsuarioController::update
* @see app/Http/Controllers/Admin/UsuarioController.php:117
* @route '/admin/usuarios/{usuario}'
*/
update.put = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::update
* @see app/Http/Controllers/Admin/UsuarioController.php:117
* @route '/admin/usuarios/{usuario}'
*/
const updateForm = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::update
* @see app/Http/Controllers/Admin/UsuarioController.php:117
* @route '/admin/usuarios/{usuario}'
*/
updateForm.put = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Admin\UsuarioController::correo
* @see app/Http/Controllers/Admin/UsuarioController.php:133
* @route '/admin/usuarios/{usuario}/correo'
*/
export const correo = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: correo.url(args, options),
    method: 'put',
})

correo.definition = {
    methods: ["put"],
    url: '/admin/usuarios/{usuario}/correo',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\UsuarioController::correo
* @see app/Http/Controllers/Admin/UsuarioController.php:133
* @route '/admin/usuarios/{usuario}/correo'
*/
correo.url = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { usuario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { usuario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            usuario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        usuario: typeof args.usuario === 'object'
        ? args.usuario.id
        : args.usuario,
    }

    return correo.definition.url
            .replace('{usuario}', parsedArgs.usuario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\UsuarioController::correo
* @see app/Http/Controllers/Admin/UsuarioController.php:133
* @route '/admin/usuarios/{usuario}/correo'
*/
correo.put = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: correo.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::correo
* @see app/Http/Controllers/Admin/UsuarioController.php:133
* @route '/admin/usuarios/{usuario}/correo'
*/
const correoForm = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: correo.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UsuarioController::correo
* @see app/Http/Controllers/Admin/UsuarioController.php:133
* @route '/admin/usuarios/{usuario}/correo'
*/
correoForm.put = (args: { usuario: number | { id: number } } | [usuario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: correo.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

correo.form = correoForm

const usuarios = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    correo: Object.assign(correo, correo),
}

export default usuarios