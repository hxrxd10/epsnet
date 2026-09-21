import ManejoDatosController from './ManejoDatosController'
import BitacoraController from './BitacoraController'
import DepartamentoController from './DepartamentoController'
import MunicipioController from './MunicipioController'
import UnidadAcademicaController from './UnidadAcademicaController'
import UsuarioController from './UsuarioController'
import CatalogoController from './CatalogoController'

const Admin = {
    ManejoDatosController: Object.assign(ManejoDatosController, ManejoDatosController),
    BitacoraController: Object.assign(BitacoraController, BitacoraController),
    DepartamentoController: Object.assign(DepartamentoController, DepartamentoController),
    MunicipioController: Object.assign(MunicipioController, MunicipioController),
    UnidadAcademicaController: Object.assign(UnidadAcademicaController, UnidadAcademicaController),
    UsuarioController: Object.assign(UsuarioController, UsuarioController),
    CatalogoController: Object.assign(CatalogoController, CatalogoController),
}

export default Admin