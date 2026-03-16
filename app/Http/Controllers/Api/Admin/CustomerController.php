import React, { useEffect, useState } from 'react';
import api from '../../services/api';
import { toast } from 'react-toastify';

const C = {
  bg:           '#f0f4f8',
  surface:      '#ffffff',
  surface2:     '#eaeff3',
  border:       '#dde3ea',
  accent:       '#1a6b6b',
  accentD:      '#145555',
  accentSoft:   '#e8f4f4',
  accentBorder: '#a8d8d8',
  text:         '#1a2332',
  muted:        '#5a6a7e',
  red:          '#e53935',
  redSoft:      '#ffebee',
  blue:         '#1565c0',
  blueSoft:     '#e3f2fd',
  navBg:        '#1e3a5f',
};

const CustomersAdmin = () => {
  const [customers,  setCustomers]  = useState([]);
  const [loading,    setLoading]    = useState(true);
  const [search,     setSearch]     = useState('');
  const [deleting,   setDeleting]   = useState(null);
  const [confirmId,  setConfirmId]  = useState(null);

  useEffect(() => { fetchCustomers(); }, [search]);

  const fetchCustomers = async () => {
    try {
      const params = search ? { search } : {};
      const response = await api.get('/admin/customers', { params });
      setCustomers(response.data.data || []);
    } catch (error) {
      console.error('Erreur:', error);
      toast.error('Erreur lors du chargement');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    setDeleting(id);
    try {
      await api.delete(`/admin/customers/${id}`);
      setCustomers(prev => prev.filter(c => c.id !== id));
      toast.success('Client supprimé');
      setConfirmId(null);
    } catch (e) {
      toast.error(e?.response?.data?.message || 'Erreur lors de la suppression');
    } finally {
      setDeleting(null);
    }
  };

  const getInitials = (name = '') =>
    name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);

  if (loading) return (
    <div style={{ display:'flex', alignItems:'center', justifyContent:'center', minHeight:'60vh' }}>
      <div style={{ textAlign:'center', color:C.muted, fontFamily:'DM Sans,sans-serif' }}>
        <div style={{ fontSize:40, animation:'spin 1s linear infinite', display:'inline-block' }}>⚙️</div>
        <div style={{ marginTop:12, fontSize:15 }}>Chargement…</div>
      </div>
    </div>
  );

  return (
    <>
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Syne:wght@700;800&display=swap');
        @keyframes spin   { to { transform:rotate(360deg); } }
        @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:none} }
        @keyframes fadeIn { from{opacity:0} to{opacity:1} }

        .ca { font-family:'DM Sans',sans-serif; background:${C.bg}; color:${C.text}; padding:28px 30px; min-height:100%; animation:fadeUp .35s ease; }
        .ca-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
        .ca-title  { font-family:'Syne',sans-serif; font-size:26px; font-weight:800; color:${C.text}; letter-spacing:-.5px; }
        .ca-count  { font-size:13px; color:${C.muted}; margin-top:3px; }
        .ca-search-wrap { background:${C.surface}; border:1px solid ${C.border}; border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:center; gap:12px; box-shadow:0 1px 4px rgba(0,0,0,.04); }
        .ca-search-icon { font-size:18px; color:${C.muted}; flex-shrink:0; }
        .ca-search { flex:1; border:none; outline:none; font-family:'DM Sans',sans-serif; font-size:14px; color:${C.text}; background:transparent; }
        .ca-search::placeholder { color:${C.muted}; }
        .ca-table-wrap { background:${C.surface}; border:1px solid ${C.border}; border-radius:16px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.04); }
        .ca-table { width:100%; border-collapse:collapse; }
        .ca-table thead th { background:${C.surface2}; padding:12px 20px; text-align:left; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:${C.muted}; }
        .ca-table tbody td { padding:14px 20px; font-size:13px; border-bottom:1px solid ${C.border}; vertical-align:middle; }
        .ca-table tbody tr:last-child td { border-bottom:none; }
        .ca-table tbody tr { transition:background .15s; }
        .ca-table tbody tr:hover td { background:${C.accentSoft}; }
        .ca-avatar { width:38px; height:38px; border-radius:10px; background:${C.accent}; color:#fff; font-family:'Syne',sans-serif; font-size:14px; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .ca-name-cell { display:flex; align-items:center; gap:12px; }
        .ca-name { font-weight:700; font-size:14px; color:${C.text}; }
        .ca-orders-badge { display:inline-flex; align-items:center; gap:5px; background:${C.blueSoft}; color:${C.blue}; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
        .ca-btn { padding:6px 12px; border-radius:7px; border:none; font-family:'DM Sans',sans-serif; font-size:12px; font-weight:700; cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:4px; }
        .ca-btn:disabled { opacity:.6; cursor:not-allowed; }
        .ca-btn-view { background:${C.accentSoft}; color:${C.accent}; border:1px solid ${C.accentBorder}; }
        .ca-btn-view:hover { background:${C.accent}; color:#fff; }
        .ca-btn-del  { background:${C.redSoft}; color:${C.red}; border:1px solid #ffcdd2; }
        .ca-btn-del:hover:not(:disabled)  { background:${C.red}; color:#fff; }
        .ca-cards { display:none; flex-direction:column; gap:12px; padding:16px; }
        .ca-card { background:${C.surface2}; border:1px solid ${C.border}; border-radius:12px; padding:16px; display:flex; flex-direction:column; gap:10px; }
        .ca-card-top { display:flex; align-items:center; gap:12px; }
        .ca-card-info { flex:1; min-width:0; }
        .ca-card-name  { font-weight:700; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .ca-card-email { font-size:12px; color:${C.muted}; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .ca-card-row   { display:flex; align-items:center; justify-content:space-between; gap:8px; }
        .ca-card-date  { font-size:12px; color:${C.muted}; }
        .ca-card-actions { display:flex; gap:8px; }
        .ca-empty { text-align:center; padding:56px 24px; color:${C.muted}; }
        .ca-empty .ei { font-size:48px; margin-bottom:12px; }
        .ca-empty p { font-size:14px; }

        /* Modal de confirmation */
        .confirm-overlay { position:fixed; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(4px); z-index:500; display:flex; align-items:center; justify-content:center; animation:fadeIn .2s ease; }
        .confirm-box { background:#fff; border-radius:16px; padding:28px; max-width:380px; width:90%; box-shadow:0 24px 64px rgba(0,0,0,.2); text-align:center; }
        .confirm-icon { font-size:48px; margin-bottom:12px; }
        .confirm-title { font-family:'Syne',sans-serif; font-size:20px; font-weight:800; color:${C.text}; margin-bottom:8px; }
        .confirm-msg { font-size:14px; color:${C.muted}; margin-bottom:24px; line-height:1.6; }
        .confirm-btns { display:flex; gap:10px; justify-content:center; }
        .confirm-btn-cancel { padding:10px 20px; border-radius:8px; border:1.5px solid ${C.border}; background:transparent; color:${C.muted}; font-family:'DM Sans',sans-serif; font-size:14px; font-weight:700; cursor:pointer; transition:all .15s; }
        .confirm-btn-cancel:hover { border-color:${C.accent}; color:${C.accent}; }
        .confirm-btn-del { padding:10px 20px; border-radius:8px; border:none; background:${C.red}; color:#fff; font-family:'DM Sans',sans-serif; font-size:14px; font-weight:700; cursor:pointer; transition:background .15s; }
        .confirm-btn-del:hover { background:#c62828; }
        .confirm-btn-del:disabled { opacity:.7; cursor:not-allowed; }

        @media (max-width: 768px) {
          .ca { padding:16px; }
          .ca-table-wrap .ca-table { display:none; }
          .ca-cards { display:flex; }
        }
        @media (max-width: 480px) {
          .ca-title { font-size:20px; }
          .ca-header { flex-direction:column; align-items:flex-start; }
        }
      `}</style>

      {/* ── Modal de confirmation suppression ── */}
      {confirmId && (
        <div className="confirm-overlay" onClick={() => setConfirmId(null)}>
          <div className="confirm-box" onClick={e => e.stopPropagation()}>
            <div className="confirm-icon">🗑️</div>
            <div className="confirm-title">Supprimer ce client ?</div>
            <div className="confirm-msg">
              Cette action est irréversible. Toutes les données associées à ce client seront supprimées.
            </div>
            <div className="confirm-btns">
              <button className="confirm-btn-cancel" onClick={() => setConfirmId(null)}>Annuler</button>
              <button
                className="confirm-btn-del"
                disabled={deleting === confirmId}
                onClick={() => handleDelete(confirmId)}
              >
                {deleting === confirmId ? 'Suppression...' : 'Supprimer'}
              </button>
            </div>
          </div>
        </div>
      )}

      <div className="ca">
        <div className="ca-header">
          <div>
            <h1 className="ca-title">👥 Gestion des clients</h1>
            <div className="ca-count">{customers.length} client{customers.length !== 1 ? 's' : ''} trouvé{customers.length !== 1 ? 's' : ''}</div>
          </div>
        </div>

        <div className="ca-search-wrap">
          <span className="ca-search-icon">🔍</span>
          <input
            className="ca-search"
            type="text"
            placeholder="Rechercher par nom ou email…"
            value={search}
            onChange={e => setSearch(e.target.value)}
          />
          {search && (
            <button onClick={() => setSearch('')} style={{ background:'none', border:'none', cursor:'pointer', color:C.muted, fontSize:16 }}>✕</button>
          )}
        </div>

        <div className="ca-table-wrap">
          {/* ── Desktop table ── */}
          <table className="ca-table">
            <thead>
              <tr>
                <th>Client</th>
                <th>Email</th>
                <th>Commandes</th>
                <th>Inscription</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {customers.map(customer => (
                <tr key={customer.id}>
                  <td>
                    <div className="ca-name-cell">
                      <div className="ca-avatar">{getInitials(customer.name)}</div>
                      <div className="ca-name">{customer.name}</div>
                    </div>
                  </td>
                  <td style={{ color:C.muted }}>{customer.email}</td>
                  <td>
                    <span className="ca-orders-badge">
                      📦 {customer.orders_count || 0} commande{(customer.orders_count || 0) !== 1 ? 's' : ''}
                    </span>
                  </td>
                  <td style={{ color:C.muted, fontSize:13 }}>
                    {new Date(customer.created_at).toLocaleDateString('fr-FR')}
                  </td>
                  <td>
                    <div style={{ display:'flex', gap:8 }}>
                      <button className="ca-btn ca-btn-view">👁️ Voir</button>
                      <button
                        className="ca-btn ca-btn-del"
                        disabled={deleting === customer.id}
                        onClick={() => setConfirmId(customer.id)}
                      >
                        {deleting === customer.id ? '...' : '🗑️'}
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          {/* ── Mobile cards ── */}
          <div className="ca-cards">
            {customers.map(customer => (
              <div key={customer.id} className="ca-card">
                <div className="ca-card-top">
                  <div className="ca-avatar">{getInitials(customer.name)}</div>
                  <div className="ca-card-info">
                    <div className="ca-card-name">{customer.name}</div>
                    <div className="ca-card-email">{customer.email}</div>
                  </div>
                </div>
                <div className="ca-card-row">
                  <span className="ca-orders-badge">📦 {customer.orders_count || 0} commande{(customer.orders_count || 0) !== 1 ? 's' : ''}</span>
                  <span className="ca-card-date">{new Date(customer.created_at).toLocaleDateString('fr-FR')}</span>
                </div>
                <div className="ca-card-actions">
                  <button className="ca-btn ca-btn-view" style={{ flex:1, justifyContent:'center' }}>👁️ Voir</button>
                  <button
                    className="ca-btn ca-btn-del"
                    disabled={deleting === customer.id}
                    onClick={() => setConfirmId(customer.id)}
                  >
                    {deleting === customer.id ? '...' : '🗑️'}
                  </button>
                </div>
              </div>
            ))}
          </div>

          {customers.length === 0 && (
            <div className="ca-empty">
              <div className="ei">🔍</div>
              <p>{search ? `Aucun client trouvé pour "${search}"` : 'Aucun client enregistré'}</p>
            </div>
          )}
        </div>
      </div>
    </>
  );
};

export default CustomersAdmin;