import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Layout from './components/Layout'
import GangList from './pages/GangList'
import NewGang from './pages/NewGang'
import GangDetail from './pages/GangDetail'
import FighterDetail from './pages/FighterDetail'

export default function App() {
  return (
    <BrowserRouter>
      <Layout>
        <Routes>
          <Route path="/"             element={<GangList />} />
          <Route path="/gangs/new"    element={<NewGang />} />
          <Route path="/gangs/:id"    element={<GangDetail />} />
          <Route path="/fighters/:id" element={<FighterDetail />} />
        </Routes>
      </Layout>
    </BrowserRouter>
  )
}
