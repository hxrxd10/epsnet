import ManejoDatosController from './ManejoDatosController'
import UsuarioController from './UsuarioController'
import CatalogoController from './CatalogoController'

const Admin = {
    ManejoDatosController: Object.assign(ManejoDatosController, ManejoDatosController),
    UsuarioController: Object.assign(UsuarioController, UsuarioController),
    CatalogoController: Object.assign(CatalogoController, CatalogoController),
}

export default Admin