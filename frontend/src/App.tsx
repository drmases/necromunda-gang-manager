import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Layout from './components/Layout'
import RequireAuth from './components/RequireAuth'
import { AuthProvider } from './AuthContext'
import GangList from './pages/GangList'
import NewGang from './pages/NewGang'
import GangDetail from './pages/GangDetail'
import FighterDetail from './pages/FighterDetail'
import FighterTemplates from './pages/FighterTemplates'
import WeaponLibrary from './pages/WeaponLibrary'
import SkillLibrary from './pages/SkillLibrary'
import InjuryLibrary from './pages/InjuryLibrary'
import Login from './pages/Login'

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter basename="/necromunda-gang-manager">
        <Layout>
          <Routes>
            <Route path="/"                element={<GangList />} />
            <Route path="/gangs/new"       element={<NewGang />} />
            <Route path="/gangs/:id"       element={<GangDetail />} />
            <Route path="/login"           element={<Login />} />
            <Route path="/fighters/:id"    element={<FighterDetail />} />
            <Route path="/templates"       element={<RequireAuth><FighterTemplates /></RequireAuth>} />
            <Route path="/weapons"         element={<RequireAuth><WeaponLibrary /></RequireAuth>} />
            <Route path="/skills"          element={<RequireAuth><SkillLibrary /></RequireAuth>} />
            <Route path="/injuries"        element={<RequireAuth><InjuryLibrary /></RequireAuth>} />
          </Routes>
        </Layout>
      </BrowserRouter>
    </AuthProvider>
  )
}
