import ManejoDatosController from './ManejoDatosController'
import BitacoraController from './BitacoraController'
import DepartamentoController from './DepartamentoController'
import MunicipioController from './MunicipioController'
import InstitucionAliadaController from './InstitucionAliadaController'
import UnidadAcademicaController from './UnidadAcademicaController'
import CarreraController from './CarreraController'
import CatalogoController from './CatalogoController'
import UsuarioController from './UsuarioController'

const Admin = {
    ManejoDatosController: Object.assign(ManejoDatosController, ManejoDatosController),
    BitacoraController: Object.assign(BitacoraController, BitacoraController),
    DepartamentoController: Object.assign(DepartamentoController, DepartamentoController),
    MunicipioController: Object.assign(MunicipioController, MunicipioController),
    InstitucionAliadaController: Object.assign(InstitucionAliadaController, InstitucionAliadaController),
    UnidadAcademicaController: Object.assign(UnidadAcademicaController, UnidadAcademicaController),
    CarreraController: Object.assign(CarreraController, CarreraController),
    CatalogoController: Object.assign(CatalogoController, CatalogoController),
    UsuarioController: Object.assign(UsuarioController, UsuarioController),
}

export default Admin