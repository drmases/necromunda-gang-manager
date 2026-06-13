import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Layout from './components/Layout'
import GangList from './pages/GangList'
import NewGang from './pages/NewGang'
import GangDetail from './pages/GangDetail'
import FighterDetail from './pages/FighterDetail'
import FighterTemplates from './pages/FighterTemplates'
import WeaponLibrary from './pages/WeaponLibrary'
import SkillLibrary from './pages/SkillLibrary'
import InjuryLibrary from './pages/InjuryLibrary'

export default function App() {
  return (
    <BrowserRouter basename="/necromunda-gang-manager">
      <Layout>
        <Routes>
          <Route path="/"                element={<GangList />} />
          <Route path="/gangs/new"       element={<NewGang />} />
          <Route path="/gangs/:id"       element={<GangDetail />} />
          <Route path="/fighters/:id"    element={<FighterDetail />} />
          <Route path="/templates"       element={<FighterTemplates />} />
          <Route path="/weapons"         element={<WeaponLibrary />} />
          <Route path="/skills"          element={<SkillLibrary />} />
          <Route path="/injuries"        element={<InjuryLibrary />} />
        </Routes>
      </Layout>
    </BrowserRouter>
  )
}
