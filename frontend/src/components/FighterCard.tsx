import { Link } from 'react-router-dom'
import type { Fighter } from '../types'

interface Props {
  fighter: Fighter
  onDelete?: (id: number) => void
}

const TYPE_COLORS: Record<string, string> = {
  'Leader':       'text-gold-400 border-gold-700',
  'Champion':     'text-gold-500 border-gold-800',
  'Ganger':       'text-dark-200 border-dark-600',
  'Juve':         'text-dark-300 border-dark-700',
  'Prospect':     'text-dark-300 border-dark-700',
  'Crew':         'text-dark-300 border-dark-700',
  'Exotic Beast': 'text-blood-500 border-blood-600',
  'Hanger-on':    'text-dark-400 border-dark-700',
}

export default function FighterCard({ fighter, onDelete }: Props) {
  const color = TYPE_COLORS[fighter.type] ?? 'text-dark-200 border-dark-600'
  const statusBadges = []
  if (fighter.dead)        statusBadges.push({ label: 'DEAD',     cls: 'bg-blood-600 text-dark-100' })
  if (fighter.in_recovery) statusBadges.push({ label: 'RECOVERY', cls: 'bg-dark-600 text-dark-300' })

  return (
    <div className={`border ${color.split(' ')[1]} bg-dark-800 rounded p-3 hover:border-gold-600 transition-colors relative group`}>
      <div className="flex justify-between items-start">
        <div>
          <Link to={`/fighters/${fighter.id}`} className="font-display text-sm hover:text-gold-400 transition-colors">
            {fighter.name}
          </Link>
          <div className={`text-xs mt-0.5 ${color.split(' ')[0]}`}>{fighter.type}</div>
        </div>
        <div className="flex flex-col items-end gap-1">
          {statusBadges.map(b => (
            <span key={b.label} className={`text-xs px-1.5 py-0.5 rounded font-mono ${b.cls}`}>{b.label}</span>
          ))}
        </div>
      </div>
      <div className="mt-2 grid grid-cols-4 gap-1 text-xs font-mono text-dark-300">
        <span title="Cost">💰 {fighter.cost}</span>
        <span title="XP">⭐ {fighter.experience}</span>
        <span title="Kills">☠ {fighter.kills}</span>
        <span title="Advancements">↑ {fighter.advancement_count}</span>
      </div>
      {onDelete && (
        <button
          onClick={() => onDelete(fighter.id)}
          className="absolute top-2 right-2 opacity-0 group-hover:opacity-100 text-dark-400 hover:text-blood-500 transition-all text-xs"
          title="Delete fighter"
        >
          ✕
        </button>
      )}
    </div>
  )
}
